<?php

namespace App\Services;

use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Support\Str;

class NewsletterSubscriberService
{
    public function normalizeEmail(string $email): string
    {
        return Str::lower(trim($email));
    }

    /**
     * @return list<string>
     */
    public function parseEmailList(string $raw): array
    {
        $parts = preg_split('/[\s,;]+/', $raw) ?: [];
        $emails = [];

        foreach ($parts as $part) {
            $email = $this->normalizeEmail($part);
            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emails[] = $email;
            }
        }

        return array_values(array_unique($emails));
    }

    public function findByEmail(string $email): ?NewsletterSubscriber
    {
        return NewsletterSubscriber::query()
            ->where('email', $this->normalizeEmail($email))
            ->first();
    }

    public function subscribe(
        string $email,
        string $source = NewsletterSubscriber::SOURCE_PUBLIC,
        ?User $user = null,
        ?User $createdBy = null,
    ): NewsletterSubscriber {
        $normalized = $this->normalizeEmail($email);

        $subscriber = NewsletterSubscriber::query()->firstOrNew(['email' => $normalized]);

        $subscriber->fill([
            'unsubscribe_token' => $subscriber->unsubscribe_token ?: bin2hex(random_bytes(32)),
            'source' => $subscriber->exists && $subscriber->isActive()
                ? $subscriber->source
                : $source,
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
            'user_id' => $user?->id ?? $subscriber->user_id,
            'created_by' => $createdBy?->id ?? $subscriber->created_by,
        ]);
        $subscriber->save();

        if ($user) {
            $user->forceFill([
                'newsletter_opt_in_at' => now(),
                'newsletter_unsubscribe_token' => $subscriber->unsubscribe_token,
            ])->save();
        }

        return $subscriber;
    }

    public function unsubscribe(NewsletterSubscriber $subscriber): void
    {
        $subscriber->forceFill([
            'unsubscribed_at' => now(),
        ])->save();

        if ($subscriber->user_id) {
            User::query()->whereKey($subscriber->user_id)->update([
                'newsletter_opt_in_at' => null,
            ]);
        }

        User::query()
            ->where('email', $subscriber->email)
            ->whereNotNull('newsletter_opt_in_at')
            ->update(['newsletter_opt_in_at' => null]);
    }

    public function unsubscribeByToken(string $token): ?NewsletterSubscriber
    {
        $subscriber = NewsletterSubscriber::query()
            ->where('unsubscribe_token', $token)
            ->first();

        if ($subscriber) {
            $this->unsubscribe($subscriber);
        }

        return $subscriber;
    }

    /**
     * @param  list<string>  $emails
     * @return array{added: int, reactivated: int, skipped: int}
     */
    public function addMany(array $emails, User $actor): array
    {
        $added = 0;
        $reactivated = 0;
        $skipped = 0;

        foreach ($emails as $email) {
            $existing = $this->findByEmail($email);
            $wasActive = $existing?->isActive() ?? false;

            if ($wasActive) {
                $skipped++;

                continue;
            }

            $this->subscribe(
                $email,
                NewsletterSubscriber::SOURCE_ADMIN,
                createdBy: $actor,
            );

            if ($existing) {
                $reactivated++;
            } else {
                $added++;
            }
        }

        return compact('added', 'reactivated', 'skipped');
    }

    public function syncUserPreference(User $user, bool $optIn): void
    {
        if ($optIn) {
            $this->subscribe(
                (string) $user->email,
                NewsletterSubscriber::SOURCE_ACCOUNT,
                user: $user,
            );

            return;
        }

        $subscriber = $this->findByEmail((string) $user->email);
        if ($subscriber) {
            $this->unsubscribe($subscriber);
        } else {
            $user->forceFill(['newsletter_opt_in_at' => null])->save();
        }
    }
}
