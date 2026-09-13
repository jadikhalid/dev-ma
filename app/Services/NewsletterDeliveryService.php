<?php

namespace App\Services;

use App\Jobs\SendNewsletterJob;
use App\Mail\NewsletterCampaignMail;
use App\Models\Newsletter;
use App\Models\NewsletterOutbox;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NewsletterDeliveryService
{
    public function __construct(
        private NewsletterSubscriberService $subscribers,
    ) {}

    /**
     * Destinataires : liste ouverte active + tous les talents approuvés (envoi systématique).
     *
     * @return Collection<int, NewsletterSubscriber>
     */
    public function recipients(): Collection
    {
        $byEmail = [];

        foreach (NewsletterSubscriber::query()->active()->orderBy('id')->cursor() as $subscriber) {
            /** @var NewsletterSubscriber $subscriber */
            $email = $this->subscribers->normalizeEmail($subscriber->email);
            $byEmail[$email] = $subscriber;
        }

        $talents = User::query()
            ->where('role', 'dev')
            ->where('approval_status', User::APPROVAL_APPROVED)
            ->whereNull('disabled_at')
            ->whereNotNull('email_verified_at')
            ->orderBy('id')
            ->cursor();

        foreach ($talents as $talent) {
            $email = $this->subscribers->normalizeEmail((string) $talent->email);
            if ($email === '') {
                continue;
            }

            $byEmail[$email] = $this->subscribers->ensureTalentRecipient($talent);
        }

        return collect(array_values($byEmail));
    }

    public function recipientCount(): int
    {
        return $this->recipients()->count();
    }

    public function schedule(Newsletter $newsletter, \DateTimeInterface $when): void
    {
        $newsletter->update([
            'status' => Newsletter::STATUS_SCHEDULED,
            'scheduled_at' => $when,
            'sent_at' => null,
        ]);

        SendNewsletterJob::dispatch($newsletter->id)
            ->delay($when);
    }

    /**
     * Queue all recipients and send the first email immediately.
     * Remaining emails are sent one per minute by the scheduler.
     */
    public function sendNow(Newsletter $newsletter): int
    {
        $queued = $this->queueDelivery($newsletter);

        if ($queued > 0) {
            $this->processNextPending();
        }

        return $queued;
    }

    /**
     * Used by scheduled delivery (job / cron).
     */
    public function deliver(Newsletter $newsletter): int
    {
        return $this->sendNow($newsletter);
    }

    public function queueDelivery(Newsletter $newsletter): int
    {
        $locked = DB::transaction(function () use ($newsletter) {
            $fresh = Newsletter::query()->whereKey($newsletter->id)->lockForUpdate()->first();
            if (! $fresh) {
                return null;
            }

            if (in_array($fresh->status, [Newsletter::STATUS_SENT, Newsletter::STATUS_SENDING], true)) {
                return null;
            }

            if ($fresh->status === Newsletter::STATUS_CANCELLED) {
                return null;
            }

            NewsletterOutbox::query()->where('newsletter_id', $fresh->id)->delete();

            $rows = [];
            $now = now();

            foreach ($this->recipients() as $subscriber) {
                $subscriber->ensureUnsubscribeToken();
                $email = $this->subscribers->normalizeEmail($subscriber->email);

                if ($email === '') {
                    continue;
                }

                $rows[] = [
                    'newsletter_id' => $fresh->id,
                    'newsletter_subscriber_id' => $subscriber->id,
                    'email' => $email,
                    'status' => NewsletterOutbox::STATUS_PENDING,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if ($rows === []) {
                $fresh->update([
                    'status' => Newsletter::STATUS_SENT,
                    'sent_at' => now(),
                    'scheduled_at' => null,
                    'recipient_count' => 0,
                ]);

                return 0;
            }

            foreach (array_chunk($rows, 200) as $chunk) {
                NewsletterOutbox::query()->insert($chunk);
            }

            $fresh->update([
                'status' => Newsletter::STATUS_SENDING,
                'scheduled_at' => null,
                'sent_at' => null,
                'recipient_count' => 0,
            ]);

            return count($rows);
        });

        return (int) $locked;
    }

    /**
     * Send exactly one pending outbox email (oldest first).
     */
    public function processNextPending(): bool
    {
        $itemId = DB::transaction(function () {
            $row = NewsletterOutbox::query()
                ->where('status', NewsletterOutbox::STATUS_PENDING)
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (! $row) {
                return null;
            }

            $row->update([
                'status' => NewsletterOutbox::STATUS_PROCESSING,
            ]);

            return $row->id;
        });

        if (! $itemId) {
            return false;
        }

        $item = NewsletterOutbox::query()->find($itemId);

        if (! $item) {
            return false;
        }

        $newsletter = Newsletter::query()->find($item->newsletter_id);

        if (! $newsletter || $newsletter->status === Newsletter::STATUS_CANCELLED) {
            $item->update([
                'status' => NewsletterOutbox::STATUS_FAILED,
                'error' => 'newsletter_unavailable',
            ]);

            return true;
        }

        $subscriber = $item->subscriber
            ?? NewsletterSubscriber::query()->where('email', $item->email)->first();

        if (! $subscriber) {
            $item->update([
                'status' => NewsletterOutbox::STATUS_FAILED,
                'error' => 'subscriber_missing',
            ]);
            $this->finalizeNewsletterIfDone($newsletter);

            return true;
        }

        try {
            $subscriber->ensureUnsubscribeToken();
            Mail::to($subscriber->email)->send(new NewsletterCampaignMail($newsletter, $subscriber));

            $item->update([
                'status' => NewsletterOutbox::STATUS_SENT,
                'error' => null,
                'sent_at' => now(),
            ]);
        } catch (Throwable $e) {
            $item->update([
                'status' => NewsletterOutbox::STATUS_FAILED,
                'error' => mb_substr($e->getMessage(), 0, 500),
                'sent_at' => null,
            ]);
        }

        $this->finalizeNewsletterIfDone($newsletter);

        return true;
    }

    public function processDueScheduled(): int
    {
        $due = Newsletter::query()
            ->where('status', Newsletter::STATUS_SCHEDULED)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->orderBy('scheduled_at')
            ->get();

        $started = 0;

        foreach ($due as $newsletter) {
            if ($this->queueDelivery($newsletter) >= 0) {
                $started++;
            }
        }

        return $started;
    }

    private function finalizeNewsletterIfDone(Newsletter $newsletter): void
    {
        $remaining = NewsletterOutbox::query()
            ->where('newsletter_id', $newsletter->id)
            ->whereIn('status', [
                NewsletterOutbox::STATUS_PENDING,
                NewsletterOutbox::STATUS_PROCESSING,
            ])
            ->exists();

        if ($remaining) {
            return;
        }

        $sent = NewsletterOutbox::query()
            ->where('newsletter_id', $newsletter->id)
            ->where('status', NewsletterOutbox::STATUS_SENT)
            ->count();

        $newsletter->update([
            'status' => Newsletter::STATUS_SENT,
            'sent_at' => now(),
            'scheduled_at' => null,
            'recipient_count' => $sent,
        ]);
    }

    public function cancelSchedule(Newsletter $newsletter): void
    {
        if (! $newsletter->isCancellable()) {
            return;
        }

        $newsletter->update([
            'status' => Newsletter::STATUS_CANCELLED,
            'scheduled_at' => null,
        ]);
    }

    /**
     * @return array{queued: int, sent: int, failed: int, pending: int}
     */
    public function progress(Newsletter $newsletter): array
    {
        $counts = NewsletterOutbox::query()
            ->where('newsletter_id', $newsletter->id)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $sent = (int) ($counts[NewsletterOutbox::STATUS_SENT] ?? 0);
        $failed = (int) ($counts[NewsletterOutbox::STATUS_FAILED] ?? 0);
        $pending = (int) ($counts[NewsletterOutbox::STATUS_PENDING] ?? 0)
            + (int) ($counts[NewsletterOutbox::STATUS_PROCESSING] ?? 0);

        return [
            'queued' => $sent + $failed + $pending,
            'sent' => $sent,
            'failed' => $failed,
            'pending' => $pending,
        ];
    }
}
