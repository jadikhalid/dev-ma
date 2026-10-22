<?php

namespace App\Services;

use App\Mail\CompanyApprovedMail;
use App\Models\CompanyProfile;
use App\Models\CompanyTrialRequest;
use App\Models\ProfessionSector;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CompanyTrialProvisioningService
{
    public function provision(CompanyTrialRequest $request, User $actor): User
    {
        if (! $request->isPending()) {
            throw ValidationException::withMessages([
                'trial' => __('talenma.admin.company_trial_requests.not_pending'),
            ]);
        }

        if (User::query()->where('email', $request->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => __('talenma.auth.validation.email_taken'),
            ]);
        }

        $plainPassword = Str::password(12);

        $user = DB::transaction(fn () => $this->createCompanyUser($request, $plainPassword, $actor));

        $this->sendCredentials($user, $plainPassword);

        return $user;
    }

    private function createCompanyUser(CompanyTrialRequest $request, string $plainPassword, ?User $actor): User
    {
        $sector = ProfessionSector::query()
            ->where('slug', $request->sector)
            ->where('is_active', true)
            ->firstOrFail();

        $sectorLabel = $sector->localizedName($request->locale ?: app()->getLocale());

        $user = User::create([
            'name' => $request->company_name,
            'first_name' => null,
            'last_name' => null,
            'email' => $request->email,
            'password' => Hash::make($plainPassword),
            'role' => 'company',
            'company_seat' => User::SEAT_OWNER,
            'email_verified_at' => now(),
            'data_processing_consent_at' => $actor === null ? now() : null,
            'data_processing_consent_version' => $actor === null
                ? (string) config('talenma.data_processing_consent_version')
                : null,
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now(),
            'approved_by' => $actor?->id,
            'rejection_reason' => null,
        ]);

        $profile = $user->companyProfile()->create([
            'representative_name' => $request->contact_name,
            'phone' => $request->phone,
            'sector' => $sectorLabel,
            'profession_sector_id' => $sector->id,
            'description' => filled($request->company_description) ? $request->company_description : null,
            'website' => $request->company_website,
            'country' => $request->company_country ?: CompanyProfile::DEFAULT_COUNTRY,
            'is_subscribed' => false,
            'subscription_expires_at' => null,
            'trial_ends_at' => null,
        ]);

        $profile->startFreeTrial();

        $request->update([
            'status' => CompanyTrialRequest::STATUS_PROVISIONED,
            'reviewed_by' => $actor?->id,
            'reviewed_at' => now(),
            'user_id' => $user->id,
            'rejection_reason' => null,
        ]);

        return $user->fresh()->loadMissing('companyProfile');
    }

    private function sendCredentials(User $user, string $plainPassword): void
    {
        try {
            Mail::to($user->email)->send(new CompanyApprovedMail($user, $plainPassword));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    public function reject(CompanyTrialRequest $request, User $actor, ?string $reason = null): void
    {
        if (! $request->isPending()) {
            throw ValidationException::withMessages([
                'trial' => __('talenma.admin.company_trial_requests.not_pending'),
            ]);
        }

        $request->update([
            'status' => CompanyTrialRequest::STATUS_REJECTED,
            'reviewed_by' => $actor->id,
            'reviewed_at' => now(),
            'rejection_reason' => filled($reason) ? trim($reason) : null,
        ]);

        // Optional notification: reuse company rejected mail shape if a User existed;
        // for requests we send a lightweight notification only when desired later.
    }
}
