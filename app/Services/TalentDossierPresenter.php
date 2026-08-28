<?php

namespace App\Services;

use App\Models\ModeratorPermissionCatalog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
            'moderatorAssignments.permissions',
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
            'is_approved_talent' => $user->isTalent() && $user->isApproved(),
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
            'approve_url' => route('admin.users.approve', $user),
            'reject_url' => route('admin.users.reject', $user),
            'can_approve' => Auth::user()?->hasModeratorPermission(ModeratorPermissionCatalog::ACCOUNTS_APPROVE) ?? false,
            'can_reject' => Auth::user()?->hasModeratorPermission(ModeratorPermissionCatalog::ACCOUNTS_REJECT) ?? false,
            'moderator' => $this->moderatorSection($user),
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
            'name' => $user->name,
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
            'approve_url' => route('admin.users.approve', $user),
            'reject_url' => route('admin.users.reject', $user),
            'can_approve' => Auth::user()?->hasModeratorPermission(ModeratorPermissionCatalog::ACCOUNTS_APPROVE) ?? false,
            'can_reject' => Auth::user()?->hasModeratorPermission(ModeratorPermissionCatalog::ACCOUNTS_REJECT) ?? false,
            'moderator' => null,
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

        $profile->loadMissing('user');

        return array_filter([
            'photo_url' => $profile->user?->avatarUrl(),
            'sector' => $profile->sectorLabel(),
            'profession' => $profile->professionLabel(),
            'specialization' => $this->text($profile->specialization),
            'bio' => $this->text($profile->bio),
            'experience_years' => $profile->experience_years !== null
                ? (string) $profile->experience_years
                : null,
            'experience_label' => $profile->experience_years !== null
                ? $profile->experienceLabel()
                : null,
            'education_level' => $this->text($profile->education_level),
            'city' => $this->text($profile->city),
            'country' => $this->text($profile->countryLabel()),
            'availability' => $profile->availability ? $profile->statusLabel() : null,
            'work_modes' => $profile->workModeLabels(),
            'languages' => $profile->languageLabels() !== []
                ? implode(', ', $profile->languageLabels())
                : null,
            'linkedin_url' => $profile->linkedin_url,
            'portfolio_url' => $profile->portfolio_url,
            'github_url' => $profile->github_url,
            'phone' => $this->text($profile->phone),
            'whatsapp' => $this->text($profile->whatsapp),
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

        $profile->loadMissing('user');

        return array_filter([
            'company_name' => $this->text($profile->user?->name),
            'sector' => $this->text($profile->sector),
            'logo_url' => $profile->logoUrl(),
            'employee_count' => $this->text($profile->employee_count),
            'city' => $this->text($profile->city),
            'country' => $this->text($profile->countryLabel()),
            'website' => $profile->website,
            'description' => $this->text($profile->description),
            'hiring_needs' => $this->text($profile->hiring_needs),
            'representative_name' => $this->text($profile->representative_name),
            'representative_photo_url' => $profile->representativePhotoUrl(),
            'email' => $this->text($profile->user?->email),
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

    /**
     * @return array<string, mixed>|null
     */
    private function moderatorSection(User $user): ?array
    {
        if (! $user->isTalent() || ! $user->isApproved()) {
            return null;
        }

        $viewer = Auth::user();
        $assignment = $user->activeModeratorAssignment();
        $canManage = $viewer?->isAdmin() ?? false;

        return [
            'is_moderator' => $assignment !== null,
            'permissions' => $assignment?->permissionKeys() ?? [],
            'permission_options' => ModeratorPermissionCatalog::options(),
            'granted_at' => $assignment?->granted_at?->translatedFormat('d M Y, H:i'),
            'can_manage' => $canManage,
            'grant_url' => $canManage ? route('admin.users.moderator.grant', $user) : null,
            'permissions_url' => $canManage ? route('admin.users.moderator.permissions', $user) : null,
            'revoke_url' => $canManage ? route('admin.users.moderator.revoke', $user) : null,
        ];
    }

    private function text(?string $value): ?string
    {
        return filled($value) ? $value : null;
    }
}
