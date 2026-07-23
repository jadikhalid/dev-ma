<?php

namespace App\Services;

use App\Mail\ConfirmPendingEmailMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PendingEmailChangeService
{
    public const TTL_MINUTES = 5;

    private const OUTCOME_TTL_MINUTES = 60;

    public function clearExpired(User $user): bool
    {
        if (! filled($user->pending_email)) {
            return false;
        }

        if ($user->pending_email_expires_at && $user->pending_email_expires_at->isFuture()) {
            return false;
        }

        $this->clear($user, 'expired');

        return true;
    }

    public function clear(User $user, ?string $outcome = null): void
    {
        if ($outcome && filled($user->pending_email_token)) {
            $this->rememberOutcome((string) $user->pending_email_token, $outcome, (int) $user->id);
        }

        $user->forceFill([
            'pending_email' => null,
            'pending_email_token' => null,
            'pending_email_expires_at' => null,
        ])->save();
    }

    public function request(User $user, string $newEmail): void
    {
        $newEmail = Str::lower(trim($newEmail));

        if ($newEmail === Str::lower($user->email)) {
            $this->clear($user);

            return;
        }

        $taken = User::query()
            ->where('id', '!=', $user->id)
            ->where(function ($query) use ($newEmail) {
                $query->where('email', $newEmail)
                    ->orWhere('pending_email', $newEmail);
            })
            ->exists();

        if ($taken) {
            throw ValidationException::withMessages([
                'email' => __('talenma.account.pending_email_taken'),
            ]);
        }

        // A new request supersedes any previous cancel/expiry outcome for an old token.
        if (filled($user->pending_email_token)) {
            Cache::forget($this->outcomeCacheKey((string) $user->pending_email_token));
        }

        $token = Str::random(64);

        $user->forceFill([
            'pending_email' => $newEmail,
            'pending_email_token' => hash('sha256', $token),
            'pending_email_expires_at' => now()->addMinutes(self::TTL_MINUTES),
        ])->save();

        $confirmUrl = route('profile.email.confirm', ['token' => $token]);

        Mail::to($newEmail)->send(new ConfirmPendingEmailMail($user, $newEmail, $confirmUrl));
    }

    /**
     * @return array{user: User}|array{error: string, user_id: int|null}
     */
    public function confirm(string $plainToken): array
    {
        $hashed = hash('sha256', $plainToken);
        $outcome = Cache::pull($this->outcomeCacheKey($hashed));

        if (is_array($outcome) && isset($outcome['reason'])) {
            return [
                'error' => match ($outcome['reason']) {
                    'cancelled' => __('talenma.account.pending_email_link_cancelled'),
                    'expired' => __('talenma.account.pending_email_expired'),
                    'confirmed' => __('talenma.account.pending_email_link_already_used'),
                    default => __('talenma.account.pending_email_invalid'),
                },
                'user_id' => isset($outcome['user_id']) ? (int) $outcome['user_id'] : null,
            ];
        }

        $user = User::query()
            ->where('pending_email_token', $hashed)
            ->first();

        if (! $user) {
            return [
                'error' => __('talenma.account.pending_email_invalid'),
                'user_id' => null,
            ];
        }

        if (! $user->pending_email_expires_at || $user->pending_email_expires_at->isPast()) {
            $this->clear($user, 'expired');

            return [
                'error' => __('talenma.account.pending_email_expired'),
                'user_id' => (int) $user->id,
            ];
        }

        $newEmail = Str::lower((string) $user->pending_email);

        $taken = User::query()
            ->where('id', '!=', $user->id)
            ->where('email', $newEmail)
            ->exists();

        if ($taken) {
            $this->clear($user);

            return [
                'error' => __('talenma.account.pending_email_taken'),
                'user_id' => (int) $user->id,
            ];
        }

        $user->forceFill([
            'email' => $newEmail,
            'email_verified_at' => now(),
            'pending_email' => null,
            'pending_email_token' => null,
            'pending_email_expires_at' => null,
        ])->save();

        $this->rememberOutcome($hashed, 'confirmed', (int) $user->id);

        return ['user' => $user];
    }

    private function rememberOutcome(string $hashedToken, string $reason, int $userId): void
    {
        Cache::put(
            $this->outcomeCacheKey($hashedToken),
            [
                'reason' => $reason,
                'user_id' => $userId,
            ],
            now()->addMinutes(self::OUTCOME_TTL_MINUTES),
        );
    }

    private function outcomeCacheKey(string $hashedToken): string
    {
        return 'pending_email_outcome:'.$hashedToken;
    }
}
