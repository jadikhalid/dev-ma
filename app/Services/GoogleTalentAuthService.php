<?php

namespace App\Services;

use App\Models\PendingRegistration;
use App\Models\PlatformSetting;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class GoogleTalentAuthService
{
    public function __construct(
        private PendingRegistrationService $pendingRegistration,
    ) {}

    /**
     * @throws ValidationException
     */
    public function authenticate(SocialiteUser $googleUser, bool $create = false): User
    {
        $googleId = trim((string) $googleUser->getId());
        $email = Str::lower(trim((string) $googleUser->getEmail()));

        if ($googleId === '' || $email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::withMessages([
                'email' => __('talenma.auth.google_email_missing'),
            ]);
        }

        if (! $this->emailIsVerified($googleUser)) {
            throw ValidationException::withMessages([
                'email' => __('talenma.auth.google_email_unverified'),
            ]);
        }

        $linked = User::query()->where('google_id', $googleId)->first();

        if ($linked) {
            $this->assertTalentCanSignIn($linked);

            return $linked;
        }

        if (! $create) {
            throw ValidationException::withMessages([
                'email' => __('talenma.auth.google_login_unknown'),
            ]);
        }

        $existing = User::query()
            ->where('email', $email)
            ->orWhere('pending_email', $email)
            ->first();

        if ($existing) {
            throw ValidationException::withMessages([
                'email' => __('talenma.auth.google_email_taken'),
            ]);
        }

        if (PendingRegistration::query()->awaitingEmailConfirmation()->where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => __('talenma.auth.google_pending'),
            ]);
        }

        return $this->createTalent($googleUser, $googleId, $email);
    }

    /**
     * @throws ValidationException
     */
    private function assertTalentCanSignIn(User $user): void
    {
        if ($user->isDisabled()) {
            throw ValidationException::withMessages([
                'email' => __('talenma.auth.account_disabled'),
            ]);
        }

        if (! $user->isTalent()) {
            throw ValidationException::withMessages([
                'email' => __('talenma.auth.google_email_taken'),
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    private function createTalent(SocialiteUser $googleUser, string $googleId, string $email): User
    {
        [$name, $firstName, $lastName] = $this->personName($googleUser);
        $autoApprove = ! PlatformSetting::requiresTalentAdminValidation();

        try {
            $user = DB::transaction(function () use ($googleId, $email, $name, $firstName, $lastName, $autoApprove) {
                $user = User::query()->create([
                    'name' => $name,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'google_id' => $googleId,
                    'password' => null,
                    'role' => 'dev',
                    'email_verified_at' => now(),
                    'data_processing_consent_at' => now(),
                    'data_processing_consent_version' => (string) config('talenma.data_processing_consent_version'),
                    'approval_status' => $autoApprove ? User::APPROVAL_APPROVED : User::APPROVAL_PENDING,
                    'approved_at' => $autoApprove ? now() : null,
                ]);

                $user->profile()->create([
                    'experience_years' => 0,
                ]);

                return $user;
            });
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages([
                'email' => __('talenma.auth.google_email_taken'),
            ]);
        }

        if ($user->isApproved()) {
            $this->pendingRegistration->sendWelcomeMail($user);
        }

        return $user;
    }

    private function emailIsVerified(SocialiteUser $googleUser): bool
    {
        $raw = $googleUser->user ?? null;

        if (! is_array($raw) || ! array_key_exists('email_verified', $raw)) {
            return true;
        }

        return filter_var($raw['email_verified'], FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return array{0: string, 1: ?string, 2: ?string}
     */
    private function personName(SocialiteUser $googleUser): array
    {
        $raw = is_array($googleUser->user ?? null) ? $googleUser->user : [];
        $first = $this->cleanPersonName((string) ($raw['given_name'] ?? ''));
        $last = $this->cleanPersonName((string) ($raw['family_name'] ?? ''));

        if ($first === '' && $last === '') {
            $parts = preg_split('/\s+/u', $this->cleanPersonName((string) $googleUser->getName()), 2) ?: [];
            $first = $this->cleanPersonName((string) ($parts[0] ?? ''));
            $last = $this->cleanPersonName((string) ($parts[1] ?? ''));
        }

        $name = trim($first.' '.$last);

        if ($name === '') {
            $name = 'Talent';
            $first = 'Talent';
            $last = '';
        }

        return [$name, $first !== '' ? $first : null, $last !== '' ? $last : null];
    }

    private function cleanPersonName(string $value): string
    {
        $value = trim(preg_replace('/\s+/u', ' ', $value) ?? '');

        return mb_substr($value, 0, 127);
    }
}
