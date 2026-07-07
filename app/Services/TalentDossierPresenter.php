<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class TalentDossierPresenter
{
    public function present(User $user): array
    {
        $user->loadMissing([
            'profile.professionSector',
            'profile.profession',
            'profile.documents',
            'approvedBy',
        ]);

        $profile = $user->profile;

        return [
            'id' => $user->id,
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
            'title' => $this->text($profile->title),
            'profession' => $profile->professionLabel(),
            'specialization' => $this->text($profile->specialization),
            'bio' => $this->text($profile->bio),
            'experience_years' => $profile->experience_years !== null
                ? (string) $profile->experience_years
                : null,
            'education_level' => $this->text($profile->education_level),
            'city' => $this->text($profile->city),
            'country' => $this->text($profile->country),
            'daily_rate_eur' => $profile->daily_rate_eur
                ? $profile->daily_rate_eur.' €'
                : null,
            'availability' => $this->text($profile->availability),
            'work_modes' => $this->workModeLabels($profile->work_modes),
            'skills' => is_array($profile->skills) && $profile->skills !== []
                ? implode(', ', $profile->skills)
                : null,
            'languages' => is_array($profile->languages) && $profile->languages !== []
                ? implode(', ', $profile->languages)
                : null,
            'linkedin_url' => $profile->linkedin_url,
            'portfolio_url' => $profile->portfolio_url,
            'github_url' => $profile->github_url,
            'phone' => $this->text($profile->phone),
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

    /**
     * @param  array<int, string>|null  $modes
     * @return list<string>
     */
    private function workModeLabels(?array $modes): array
    {
        if (! is_array($modes) || $modes === []) {
            return [];
        }

        $labels = [
            'remote' => __('talenma.talent.work_mode_remote'),
            'visa_sponsorship' => __('talenma.talent.work_mode_visa'),
            'local' => __('talenma.talent.work_mode_local'),
        ];

        return array_values(array_map(
            fn (string $mode) => $labels[$mode] ?? $mode,
            $modes,
        ));
    }
}
