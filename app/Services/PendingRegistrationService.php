<?php

namespace App\Services;

use App\Http\Requests\Auth\RegisterRequest;
use App\Jobs\PurgePendingRegistrationJob;
use App\Mail\VerifyRegistrationMail;
use App\Models\PendingRegistration;
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
    public const EXPIRY_MINUTES = 5;

    /**
     * @throws ValidationException
     */
    public function createFromRequest(RegisterRequest $request): PendingRegistration
    {
        $validated = $request->validated();

        $this->purgeExistingForEmail($validated['email']);

        $pending = PendingRegistration::query()->create([
            'token' => PendingRegistration::generateToken(),
            'email' => $validated['email'],
            'locale' => app()->getLocale(),
            'payload' => $this->buildPayload($validated),
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
        ]);

        if ($validated['role'] === 'dev') {
            $pending->update([
                'document_paths' => $this->storeDocuments($pending, $request->file('documents', [])),
            ]);
        }

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

    private function sendVerificationMail(PendingRegistration $pending): void
    {
        $verificationUrl = URL::route('register.verify', ['token' => $pending->token], absolute: true);

        Mail::to($pending->email)
            ->locale($pending->locale)
            ->sendNow(new VerifyRegistrationMail($pending, $verificationUrl));
    }

    public function complete(string $token): User
    {
        $pending = PendingRegistration::query()->where('token', $token)->first();

        if (! $pending) {
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

        if (User::query()->where('email', $pending->email)->exists()) {
            $this->purge($pending);

            throw ValidationException::withMessages([
                'email' => __('talenma.auth.validation.email_taken'),
            ]);
        }

        return DB::transaction(function () use ($pending) {
            $payload = $pending->payload;

            $user = User::create([
                'name' => $payload['name'],
                'first_name' => $payload['first_name'] ?? null,
                'last_name' => $payload['last_name'] ?? null,
                'email' => $pending->email,
                'password' => $payload['password'],
                'role' => $payload['role'],
                'email_verified_at' => now(),
                'approval_status' => $payload['role'] === 'dev' ? User::APPROVAL_PENDING : User::APPROVAL_APPROVED,
                'approved_at' => $payload['role'] === 'company' ? now() : null,
            ]);

            if ($user->role === 'company') {
                $user->companyProfile()->create([
                    'company_name' => $payload['name'],
                    'representative_name' => $payload['representative_name'],
                    'representative_email' => $payload['representative_email'],
                    'hiring_needs' => $payload['company_need'],
                    'country' => 'France',
                ]);
            }

            if ($user->role === 'dev') {
                $sector = ProfessionSector::query()
                    ->where('slug', $payload['sector'])
                    ->where('is_active', true)
                    ->firstOrFail();

                $profile = $user->profile()->create([
                    'profession_sector_id' => $sector->id,
                    'registration_description' => $payload['description'],
                    'experience_years' => 0,
                    'country' => 'Maroc',
                ]);

                $this->attachDocuments($profile, $pending);
            }

            $this->purge($pending);

            return $user;
        });
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
        $payload = [
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ];

        if ($validated['role'] === 'dev') {
            $firstName = $validated['first_name'];
            $lastName = $validated['last_name'];

            $payload['first_name'] = $firstName;
            $payload['last_name'] = $lastName;
            $payload['name'] = trim($firstName.' '.$lastName);
            $payload['sector'] = $validated['sector'];
            $payload['description'] = $validated['description'];
        }

        if ($validated['role'] === 'company') {
            $payload['name'] = $validated['name'];
            $payload['representative_name'] = $validated['representative_name'];
            $payload['representative_email'] = $validated['representative_email'];
            $payload['company_need'] = $validated['company_need'];
        }

        return $payload;
    }

    /**
     * @param  list<UploadedFile>  $files
     * @return list<array<string, mixed>>
     */
    private function storeDocuments(PendingRegistration $pending, array $files): array
    {
        $stored = [];

        foreach ($files as $index => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store(
                'pending-registrations/'.$pending->id,
                'local',
            );

            $stored[] = [
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
                'size' => (int) $file->getSize(),
                'sort_order' => $index + 1,
            ];
        }

        return $stored;
    }

    private function attachDocuments(\App\Models\Profile $profile, PendingRegistration $pending): void
    {
        foreach ($pending->document_paths ?? [] as $document) {
            $sourcePath = $document['path'] ?? null;

            if (! is_string($sourcePath) || ! Storage::disk('local')->exists($sourcePath)) {
                continue;
            }

            $extension = pathinfo($document['original_name'] ?? 'file', PATHINFO_EXTENSION) ?: 'bin';
            $destination = 'profile-documents/'.$profile->id.'/'.uniqid('doc_', true).'.'.$extension;

            Storage::disk('public')->writeStream(
                $destination,
                Storage::disk('local')->readStream($sourcePath),
            );

            ProfileDocument::query()->create([
                'profile_id' => $profile->id,
                'path' => $destination,
                'original_name' => $document['original_name'],
                'mime_type' => $document['mime_type'],
                'size' => $document['size'],
                'sort_order' => $document['sort_order'],
            ]);
        }
    }

    private function deleteDocumentStorage(PendingRegistration $pending): void
    {
        Storage::disk('local')->deleteDirectory('pending-registrations/'.$pending->id);
    }
}
