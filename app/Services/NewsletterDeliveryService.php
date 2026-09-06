<?php

namespace App\Services;

use App\Jobs\SendNewsletterJob;
use App\Mail\NewsletterCampaignMail;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NewsletterDeliveryService
{
    public function __construct(
        private NewsletterRenderer $renderer,
    ) {}

    /**
     * @return \Illuminate\Database\Eloquent\Builder<NewsletterSubscriber>
     */
    public function recipientsQuery()
    {
        return NewsletterSubscriber::query()->active();
    }

    public function recipientCount(): int
    {
        return $this->recipientsQuery()->count();
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

    public function sendNow(Newsletter $newsletter): int
    {
        return $this->deliver($newsletter);
    }

    public function deliver(Newsletter $newsletter): int
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

            $fresh->update([
                'status' => Newsletter::STATUS_SENDING,
            ]);

            return $fresh;
        });

        if (! $locked) {
            return 0;
        }

        $sent = 0;
        $this->recipientsQuery()->orderBy('id')->chunkById(50, function ($subscribers) use ($locked, &$sent): void {
            foreach ($subscribers as $subscriber) {
                try {
                    /** @var NewsletterSubscriber $subscriber */
                    $subscriber->ensureUnsubscribeToken();
                    Mail::to($subscriber->email)->send(new NewsletterCampaignMail($locked, $subscriber));
                    $sent++;
                } catch (Throwable) {
                    // Continue remaining recipients.
                }
            }
        });

        $locked->update([
            'status' => Newsletter::STATUS_SENT,
            'sent_at' => now(),
            'scheduled_at' => null,
            'recipient_count' => $sent,
        ]);

        return $sent;
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
}
