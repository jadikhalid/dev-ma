<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class TalentDossierPresenter
{
    public function present(User $user): array
    {
        if ($user->isCompany()) {
            return $this->presentCompany($user);
        }

        $user->loadMissing([
            'profile.professionSector',
            'profile.profession',
            'profile.documents',
            'approvedBy',
        ]);

        $profile = $user->profile;

        return [
            'id' => $user->id,
            'role' => $user->role,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'email_verified' => $user->hasVerifiedEmail(),
            'registered_at' => $user->created_at?->translatedFormat('d M Y, H:i'),
            'approval_status' => $user->approval_status,
            'approval_status_label' => $this->approvalStatusLabel($user),
            'approved_at' => $user->approved_at?->translatedFormat('d M Y, H:i'),
            'approved_by' => $user->approvedBy?->name,
            'is_pending' => $user->isPendingApproval(),
            'sector' => $profile?->sectorLabel() ?? '—',
            'description' => $profile?->registration_description ?? '—',
            'documents' => $profile?->documents
                ->map(fn ($document) => [
                    'id' => $document->id,
                    'name' => $document->original_name,
                    'url' => $document->url(),
                    'size' => $document->formattedSize(),
                    'is_image' => Str::startsWith($document->mime_type ?? '', 'image/'),
                ])
                ->values()
                ->all(),
            'current_profile' => $this->currentProfile($profile),
            'company' => null,
            'approve_url' => route('admin.users.approve', $user),
            'reject_url' => route('admin.users.reject', $user),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentCompany(User $user): array
    {
        $user->loadMissing(['companyProfile.documents', 'approvedBy']);

        $company = $user->companyProfile;

        return [
            'id' => $user->id,
            'role' => $user->role,
            'name' => $company?->company_name ?? $user->name,
            'first_name' => null,
            'last_name' => null,
            'email' => $user->email,
            'email_verified' => $user->hasVerifiedEmail(),
            'registered_at' => $user->created_at?->translatedFormat('d M Y, H:i'),
            'approval_status' => $user->approval_status,
            'approval_status_label' => $this->approvalStatusLabel($user),
            'approved_at' => $user->approved_at?->translatedFormat('d M Y, H:i'),
            'approved_by' => $user->approvedBy?->name,
            'is_pending' => $user->isPendingApproval(),
            'sector' => filled($company?->registration_sector) ? $company->registration_sector : ($company?->sector ?? '—'),
            'description' => filled($company?->registration_hiring_needs) ? $company->registration_hiring_needs : '—',
            'documents' => $company?->documents
                ->map(fn ($document) => [
                    'id' => $document->id,
                    'name' => $document->original_name,
                    'url' => $document->url(),
                    'size' => $document->formattedSize(),
                    'is_image' => Str::startsWith($document->mime_type ?? '', 'image/'),
                ])
                ->values()
                ->all() ?? [],
            'current_profile' => $this->currentCompanyProfile($company),
            'company' => $company ? array_filter([
                'company_name' => $this->text($company->company_name),
                'representative_name' => $this->text($company->representative_name),
                'representative_email' => $this->text($company->representative_email),
                'website' => $this->text($company->website),
                'country' => $this->text($company->country),
                'city' => $this->text($company->city),
                'employee_count' => $this->text($company->employee_count),
                'hiring_needs' => $this->text($company->registration_hiring_needs ?? $company->hiring_needs),
            ], fn ($value) => filled($value)) : [],
            'approve_url' => route('admin.users.approve', $user),
            'reject_url' => route('admin.users.reject', $user),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function currentProfile(?\App\Models\Profile $profile): array
    {
        if (! $profile) {
            return [];
        }

        return array_filter([
            'profession' => $profile->professionLabel(),
            'specialization' => $this->text($profile->specialization),
            'bio' => $this->text($profile->bio),
            'experience_years' => $profile->experience_years !== null
                ? (string) $profile->experience_years
                : null,
            'education_level' => $this->text($profile->education_level),
            'city' => $this->text($profile->city),
            'country' => $this->text($profile->country),
            'availability' => $profile->availability ? $profile->statusLabel() : null,
            'work_modes' => $profile->workModeLabels(),
            'skills' => is_array($profile->skills) && $profile->skills !== []
                ? implode(', ', $profile->skills)
                : null,
            'languages' => $profile->languageLabels() !== []
                ? implode(', ', $profile->languageLabels())
                : null,
            'linkedin_url' => $profile->linkedin_url,
            'portfolio_url' => $profile->portfolio_url,
            'github_url' => $profile->github_url,
            'phone' => $this->text($profile->phone),
        ], fn ($value) => filled($value));
    }

    /**
     * @return array<string, mixed>
     */
    private function currentCompanyProfile(?\App\Models\CompanyProfile $profile): array
    {
        if (! $profile) {
            return [];
        }

        return array_filter([
            'company_name' => $this->text($profile->company_name),
            'sector' => $this->text($profile->sector),
            'logo_url' => $profile->logoUrl(),
            'employee_count' => $this->text($profile->employee_count),
            'city' => $this->text($profile->city),
            'country' => $this->text($profile->country),
            'website' => $profile->website,
            'description' => $this->text($profile->description),
            'hiring_needs' => $this->text($profile->hiring_needs),
            'representative_name' => $this->text($profile->representative_name),
            'representative_email' => $this->text($profile->representative_email),
            'phone' => $this->text($profile->phone),
            'linkedin_url' => $profile->linkedin_url,
        ], fn ($value) => filled($value));
    }

    private function approvalStatusLabel(User $user): string
    {
        if ($user->isPendingApproval()) {
            return __('talenma.admin.users.status_pending');
        }

        if ($user->isRejected()) {
            return __('talenma.admin.users.status_rejected');
        }

        if ($user->isApproved()) {
            return __('talenma.admin.users.status_approved');
        }

        return '—';
    }

    private function text(?string $value): ?string
    {
        return filled($value) ? $value : null;
    }

}
