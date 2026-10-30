<?php

namespace App\Services;

use App\Http\Requests\Auth\RegisterRequest;
use App\Jobs\PurgePendingRegistrationJob;
use App\Mail\TalentWelcomeMail;
use App\Mail\VerifyRegistrationMail;
use App\Models\CompanyProfileDocument;
use App\Models\CompanyTrialRequest;
use App\Models\PendingRegistration;
use App\Models\PlatformSetting;
use App\Models\ProfessionSector;
use App\Models\ProfileDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PendingRegistrationService
{
    /** Lien de confirmation d'inscription (24 heures). */
    public const EXPIRY_HOURS = 24;

    public const EXPIRY_MINUTES = self::EXPIRY_HOURS * 60;

    /**
     * @throws ValidationException
     */
    public function createFromRequest(RegisterRequest $request): PendingRegistration
    {
        $validated = $request->validated();

        if (($validated['role'] ?? null) === 'company') {
            throw ValidationException::withMessages([
                'role' => __('talenma.auth.validation.role_invalid'),
            ]);
        }

        $this->purgeExistingForEmail($validated['email']);

        $pending = PendingRegistration::query()->create([
            'token' => PendingRegistration::generateToken(),
            'email' => $validated['email'],
            'locale' => app()->getLocale(),
            'payload' => $this->buildPayload($validated),
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
        ]);

        $cv = $request->file('cv');

        $pending->update([
            'document_paths' => $cv instanceof UploadedFile
                ? [$this->storePendingDocument(
                    $pending,
                    $cv,
                    1,
                    ProfileDocument::TYPE_CV,
                    $validated['cv_language'] ?? null,
                )]
                : [],
        ]);

        try {
            $this->sendVerificationMail($pending);
        } catch (\Throwable $exception) {
            $this->purge($pending);
            throw $exception;
        }

        PurgePendingRegistrationJob::dispatch($pending->id)
            ->delay(now()->addMinutes(self::EXPIRY_MINUTES));

        return $pending;
    }

    /**
     * Demande d'essai entreprise : le compte n'est créé qu'après confirmation de l'e-mail.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws \Throwable
     */
    public function createCompanyTrial(array $data, ?string $ip): PendingRegistration
    {
        $this->purgeExistingForEmail($data['email']);

        $pending = PendingRegistration::query()->create([
            'token' => PendingRegistration::generateToken(),
            'email' => $data['email'],
            'locale' => app()->getLocale(),
            'payload' => [
                'role' => 'company',
                'company_trial' => true,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'name' => $data['company_name'],
                'representative_name' => $data['contact_name'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'],
                'sector' => $data['sector'],
                'company_description' => filled($data['company_description'] ?? null) ? $data['company_description'] : null,
                'company_website' => filled($data['company_website'] ?? null) ? $data['company_website'] : null,
                'company_country' => $data['company_country'],
                'ip_address' => $ip,
                'trial_terms_accepted_at' => now()->toIso8601String(),
                'trial_terms_version' => (string) config('talenma.trial_terms_version'),
                'data_processing_consent_at' => now()->toIso8601String(),
                'data_processing_consent_version' => (string) config('talenma.data_processing_consent_version'),
            ],
            'document_paths' => [],
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
        ]);

        try {
            $this->sendVerificationMail($pending);
        } catch (\Throwable $exception) {
            $this->purge($pending);

            throw $exception;
        }

        PurgePendingRegistrationJob::dispatch($pending->id)
            ->delay(now()->addMinutes(self::EXPIRY_MINUTES));

        return $pending;
    }

    /**
     * Inscription talent immédiate (sans e-mail de confirmation) — phase de lancement.
     *
     * @throws ValidationException
     */
    public function registerTalentImmediately(RegisterRequest $request): User
    {
        $validated = $request->validated();

        if (($validated['role'] ?? null) !== 'dev') {
            throw ValidationException::withMessages([
                'role' => __('talenma.auth.validation.role_invalid'),
            ]);
        }

        $this->purgeExistingForEmail($validated['email']);

        $pending = PendingRegistration::query()->create([
            'token' => PendingRegistration::generateToken(),
            'email' => $validated['email'],
            'locale' => app()->getLocale(),
            'payload' => $this->buildPayload($validated),
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
        ]);

        $cv = $request->file('cv');

        $pending->update([
            'document_paths' => $cv instanceof UploadedFile
                ? [$this->storePendingDocument(
                    $pending,
                    $cv,
                    1,
                    ProfileDocument::TYPE_CV,
                    $validated['cv_language'] ?? null,
                )]
                : [],
        ]);

        return $this->finalize($pending->fresh() ?? $pending);
    }

    /**
     * @throws ValidationException
     */
    public function resendVerificationEmail(string $email): PendingRegistration
    {
        $pending = PendingRegistration::query()
            ->where('email', Str::lower(trim($email)))
            ->first();

        if (! $pending || $pending->isExpired()) {
            throw ValidationException::withMessages([
                'email' => __('talenma.auth.registration_resend_unavailable'),
            ]);
        }

        $this->sendVerificationMail($pending);

        return $pending;
    }

    /**
     * Annule une inscription en attente via le lien du mail (destinataire involontaire).
     *
     * @throws ValidationException
     */
    public function cancelByToken(string $token): void
    {
        $pending = PendingRegistration::query()->where('token', $token)->first();

        if (! $pending || $pending->isCompleted()) {
            throw ValidationException::withMessages([
                'token' => __('talenma.auth.registration_cancel_invalid'),
            ]);
        }

        if ($pending->isExpired()) {
            $this->purge($pending);

            throw ValidationException::withMessages([
                'token' => __('talenma.auth.registration_cancel_expired'),
            ]);
        }

        $this->purge($pending);
    }

    private function sendVerificationMail(PendingRegistration $pending): void
    {
        $buildUrls = function () use ($pending): array {
            return [
                URL::route('register.verify', ['token' => $pending->token], absolute: true),
                URL::route('register.cancel', ['token' => $pending->token], absolute: true),
            ];
        };

        // Company accounts only have a session on the entreprises host.
        [$verificationUrl, $cancellationUrl] = ($pending->payload['role'] ?? null) === 'company'
            ? \App\Support\PortalHost::onCompanyHost($buildUrls)
            : \App\Support\PortalHost::onWwwHost($buildUrls);

        Mail::to($pending->email)
            ->locale($pending->locale)
            ->sendNow(new VerifyRegistrationMail($pending, $verificationUrl, $cancellationUrl));
    }

    public function sendWelcomeMail(User $user, ?string $locale = null): void
    {
        try {
            Mail::to($user->email)
                ->locale($locale ?: app()->getLocale())
                ->sendNow(new TalentWelcomeMail($user));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    public function complete(string $token): User
    {
        $pending = PendingRegistration::query()->where('token', $token)->first();

        if (! $pending) {
            throw ValidationException::withMessages([
                'token' => __('talenma.auth.registration_verify_invalid'),
            ]);
        }

        // Idempotent: email clients often prefetch the link once before the user clicks.
        if ($pending->isCompleted()) {
            $user = User::query()->find($pending->user_id);

            if ($user) {
                return $user;
            }

            throw ValidationException::withMessages([
                'token' => __('talenma.auth.registration_verify_invalid'),
            ]);
        }

        if ($pending->isExpired()) {
            $this->purge($pending);

            throw ValidationException::withMessages([
                'token' => __('talenma.auth.registration_verify_expired'),
            ]);
        }

        return $this->finalize($pending);
    }

    /**
     * Valide une inscription en attente côté admin (sans connexion auto).
     * Accepte aussi les liens expirés encore présents en base.
     *
     * @throws ValidationException
     */
    public function completeForAdmin(PendingRegistration $pending): User
    {
        return $this->finalize($pending);
    }

    /**
     * Renvoie l'e-mail de vérification ; prolonge la validité si le lien a expiré.
     */
    public function resendForAdmin(PendingRegistration $pending): PendingRegistration
    {
        if ($pending->isExpired()) {
            $pending->update([
                'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
            ]);

            PurgePendingRegistrationJob::dispatch($pending->id)
                ->delay(now()->addMinutes(self::EXPIRY_MINUTES));
        }

        $this->sendVerificationMail($pending->fresh() ?? $pending);

        return $pending->fresh() ?? $pending;
    }

    /**
     * @throws ValidationException
     */
    private function finalize(PendingRegistration $pending): User
    {
        $existing = User::query()->where('email', $pending->email)->first();

        if ($existing) {
            // Another hit (prefetch / double-click) already created the account.
            $this->markCompleted($pending, $existing);

            return $existing;
        }

        $locale = $pending->locale;

        $user = DB::transaction(function () use ($pending) {
            $payload = $pending->payload;
            $isTalent = ($payload['role'] ?? null) === 'dev';
            $isCompanyTrial = ($payload['role'] ?? null) === 'company' && ! empty($payload['company_trial']);
            $autoApprove = $isCompanyTrial || ($isTalent && ! PlatformSetting::requiresTalentAdminValidation());

            $user = User::create([
                'name' => $payload['name'],
                'first_name' => $payload['first_name'] ?? null,
                'last_name' => $payload['last_name'] ?? null,
                'email' => $pending->email,
                'password' => $payload['password'],
                'role' => $payload['role'],
                'company_seat' => $payload['role'] === 'company' ? User::SEAT_OWNER : null,
                'email_verified_at' => now(),
                'data_processing_consent_at' => isset($payload['data_processing_consent_at'])
                    ? \Illuminate\Support\Carbon::parse($payload['data_processing_consent_at'])
                    : null,
                'data_processing_consent_version' => $payload['data_processing_consent_version'] ?? null,
                'approval_status' => $autoApprove ? User::APPROVAL_APPROVED : User::APPROVAL_PENDING,
                'approved_at' => $autoApprove ? now() : null,
            ]);

            if ($user->role === 'company') {
                $sector = ProfessionSector::query()
                    ->where('slug', $payload['sector'])
                    ->where('is_active', true)
                    ->firstOrFail();

                $sectorLabel = $sector->localizedName($pending->locale);

                $companyProfile = $user->companyProfile()->create([
                    'representative_name' => $payload['representative_name'] ?? null,
                    'phone' => $payload['phone'] ?? null,
                    'sector' => $sectorLabel,
                    'profession_sector_id' => $sector->id,
                    'description' => $payload['company_description'] ?? null,
                    'website' => $payload['company_website'] ?? null,
                    'country' => $payload['company_country'] ?? \App\Models\CompanyProfile::DEFAULT_COUNTRY,
                    'is_subscribed' => false,
                    'subscription_expires_at' => null,
                    'trial_ends_at' => null,
                ]);

                $this->attachCompanyDocuments($companyProfile, $pending);

                if ($isCompanyTrial) {
                    $companyProfile->startFreeTrial();

                    CompanyTrialRequest::query()->create([
                        'company_name' => $payload['name'],
                        'contact_name' => $payload['representative_name'] ?? $payload['name'],
                        'email' => $pending->email,
                        'phone' => $payload['phone'] ?? '',
                        'sector' => $payload['sector'],
                        'company_description' => filled($payload['company_description'] ?? null) ? $payload['company_description'] : '',
                        'company_website' => $payload['company_website'] ?? null,
                        'company_country' => $payload['company_country'],
                        'status' => CompanyTrialRequest::STATUS_PROVISIONED,
                        'reviewed_at' => now(),
                        'user_id' => $user->id,
                        'locale' => $pending->locale,
                        'ip_address' => $payload['ip_address'] ?? null,
                        'trial_terms_accepted_at' => $payload['trial_terms_accepted_at'] ?? now(),
                        'trial_terms_version' => $payload['trial_terms_version'] ?? null,
                    ]);
                }
            }

            if ($user->role === 'dev') {
                $sectorId = null;
                $sectorSlug = $payload['sector'] ?? null;

                if (filled($sectorSlug)) {
                    $sectorId = ProfessionSector::query()
                        ->where('slug', $sectorSlug)
                        ->where('is_active', true)
                        ->value('id');
                }

                $profile = $user->profile()->create([
                    'profession_sector_id' => $sectorId,
                    'bio' => $payload['description'] ?? null,
                    'experience_years' => 0,
                ]);

                $this->attachDocuments($profile, $pending);
            }

            $this->markCompleted($pending, $user);

            return $user;
        });

        if ($user->isTalent() && $user->isApproved()) {
            $this->sendWelcomeMail($user, $locale);
        }

        return $user;
    }

    private function markCompleted(PendingRegistration $pending, User $user): void
    {
        $this->deleteDocumentStorage($pending);

        $payload = $pending->payload ?? [];
        unset($payload['password']);

        $pending->forceFill([
            'completed_at' => now(),
            'user_id' => $user->id,
            'payload' => $payload,
            'document_paths' => null,
        ])->save();
    }

    public function purge(PendingRegistration $pending): void
    {
        $this->deleteDocumentStorage($pending);
        $pending->delete();
    }

    public function purgeExpired(): int
    {
        $count = 0;

        PendingRegistration::query()
            ->whereNull('completed_at')
            ->where('expires_at', '<', now())
            ->each(function (PendingRegistration $pending) use (&$count) {
                $this->purge($pending);
                $count++;
            });

        return $count;
    }

    public function purgeForEmail(string $email): void
    {
        PendingRegistration::query()
            ->where('email', Str::lower(trim($email)))
            ->each(fn (PendingRegistration $pending) => $this->purge($pending));
    }

    private function purgeExistingForEmail(string $email): void
    {
        $this->purgeForEmail($email);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function buildPayload(array $validated): array
    {
        $firstName = $validated['first_name'];
        $lastName = $validated['last_name'];

        return [
            'role' => 'dev',
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => trim($firstName.' '.$lastName),
            'password' => Hash::make($validated['password']),
            'sector' => $validated['sector'] ?? null,
            'description' => $validated['description'] ?? null,
            'data_processing_consent_at' => now()->toIso8601String(),
            'data_processing_consent_version' => (string) config('talenma.data_processing_consent_version'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function storePendingDocument(
        PendingRegistration $pending,
        UploadedFile $file,
        int $sortOrder,
        string $documentType = ProfileDocument::TYPE_REGISTRATION,
        ?string $language = null,
    ): array {
        $path = $file->store(
            'pending-registrations/'.$pending->id,
            'local',
        );

        if ($documentType === ProfileDocument::TYPE_CV && filled($language)) {
            $languageIndex = array_search($language, ProfileDocument::CV_LANGUAGES, true);
            $sortOrder = $languageIndex === false ? $sortOrder : ($languageIndex + 1);
        }

        return [
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
            'size' => (int) $file->getSize(),
            'sort_order' => $sortOrder,
            'document_type' => $documentType,
            'language' => $language,
        ];
    }

    private function attachDocuments(\App\Models\Profile $profile, PendingRegistration $pending): void
    {
        foreach ($pending->document_paths ?? [] as $document) {
            $documentType = $document['document_type'] ?? ProfileDocument::TYPE_REGISTRATION;
            $language = $document['language'] ?? null;

            $this->copyDocumentToProfile($document, function (array $attrs) use ($profile, $documentType, $language) {
                ProfileDocument::query()->create(array_merge($attrs, [
                    'profile_id' => $profile->id,
                    'document_type' => $documentType,
                    'language' => $language,
                ]));
            }, 'profile-documents/'.$profile->id);
        }
    }

    private function attachCompanyDocuments(\App\Models\CompanyProfile $companyProfile, PendingRegistration $pending): void
    {
        foreach ($pending->document_paths ?? [] as $document) {
            $this->copyDocumentToProfile($document, function (array $attrs) use ($companyProfile) {
                CompanyProfileDocument::query()->create(array_merge($attrs, [
                    'company_profile_id' => $companyProfile->id,
                ]));
            }, 'company-profile-documents/'.$companyProfile->id);
        }
    }

    /**
     * @param  array<string, mixed>  $document
     * @param  callable(array<string, mixed>): void  $createRecord
     */
    private function copyDocumentToProfile(array $document, callable $createRecord, string $destinationDirectory): void
    {
        $sourcePath = $document['path'] ?? null;

        if (! is_string($sourcePath) || ! Storage::disk('local')->exists($sourcePath)) {
            return;
        }

        $extension = pathinfo($document['original_name'] ?? 'file', PATHINFO_EXTENSION) ?: 'bin';
        $destination = $destinationDirectory.'/'.uniqid('doc_', true).'.'.$extension;

        Storage::disk('public')->writeStream(
            $destination,
            Storage::disk('local')->readStream($sourcePath),
        );

        $createRecord([
            'path' => $destination,
            'original_name' => $document['original_name'],
            'mime_type' => $document['mime_type'],
            'size' => $document['size'],
            'sort_order' => $document['sort_order'],
        ]);
    }

    private function deleteDocumentStorage(PendingRegistration $pending): void
    {
        Storage::disk('local')->deleteDirectory('pending-registrations/'.$pending->id);
    }
}
