<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'first_name',
    'last_name',
    'email',
    'pending_email',
    'pending_email_token',
    'pending_email_expires_at',
    'avatar_path',
    'password',
    'role',
    'company_seat',
    'email_verified_at',
    'approval_status',
    'approved_at',
    'approved_by',
    'rejection_reason',
    'disabled_at',
    'is_subscribed',
    'subscription_expires_at',
])]
#[Hidden(['password', 'remember_token', 'pending_email_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    public const APPROVAL_PENDING = 'pending';

    public const APPROVAL_APPROVED = 'approved';

    public const APPROVAL_REJECTED = 'rejected';

    public const SEAT_OWNER = 'owner';

    public const SEAT_MEMBER = 'member';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'pending_email_expires_at' => 'datetime',
            'approved_at' => 'datetime',
            'disabled_at' => 'datetime',
            'password' => 'hashed',
            'is_subscribed' => 'boolean',
            'subscription_expires_at' => 'datetime',
        ];
    }

    public function hasPendingEmailChange(): bool
    {
        return filled($this->pending_email)
            && $this->pending_email_expires_at
            && $this->pending_email_expires_at->isFuture();
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function companyProfile(): HasOne
    {
        return $this->hasOne(CompanyProfile::class);
    }

    public function companyMembership(): HasOne
    {
        return $this->hasOne(CompanyMembership::class);
    }

    public function recruitmentRequests(): HasMany
    {
        return $this->hasMany(RecruitmentRequest::class, 'company_user_id');
    }

    public function companyDirectHireRequests(): HasMany
    {
        return $this->hasMany(DirectHireRequest::class, 'company_user_id');
    }

    public function talentDirectHireRequests(): HasMany
    {
        return $this->hasMany(DirectHireRequest::class, 'talent_user_id');
    }

    public function companyConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'company_user_id');
    }

    public function talentConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'talent_user_id');
    }

    public function moderationRequests(): HasMany
    {
        return $this->hasMany(ModerationRequest::class, 'requested_by');
    }

    public function jobPostingsCreated(): HasMany
    {
        return $this->hasMany(JobPosting::class, 'created_by');
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'talent_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isTalent(): bool
    {
        return $this->role === 'dev';
    }

    /** @deprecated Use isTalent() */
    public function isDeveloper(): bool
    {
        return $this->isTalent();
    }

    public function isCompany(): bool
    {
        return $this->role === 'company';
    }

    public function isCompanyOwner(): bool
    {
        return $this->isCompany() && $this->company_seat === self::SEAT_OWNER;
    }

    public function isCompanyMember(): bool
    {
        return $this->isCompany() && $this->company_seat === self::SEAT_MEMBER;
    }

    public function isDisabled(): bool
    {
        return $this->disabled_at !== null;
    }

    public function companyOrganization(): ?CompanyProfile
    {
        if (! $this->isCompany()) {
            return null;
        }

        if ($this->isCompanyOwner()) {
            return $this->companyProfile;
        }

        $this->loadMissing('companyMembership.companyProfile.user');

        return $this->companyMembership?->companyProfile;
    }

    public function canManageCompanyProfile(): bool
    {
        return $this->isCompanyOwner() && $this->isApproved() && ! $this->isDisabled();
    }

    public function canManageCompanyUsers(): bool
    {
        return $this->canManageCompanyProfile();
    }

    public function canAccessTalentPool(): bool
    {
        return $this->isCompany() && $this->isApproved() && ! $this->isDisabled();
    }

    public function canManageJobs(): bool
    {
        return $this->canAccessTalentPool();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isModerator(): bool
    {
        return $this->role === 'moderator';
    }

    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->isModerator();
    }

    public function isApproved(): bool
    {
        if ($this->isStaff()) {
            return true;
        }

        if ($this->isTalent() || $this->isCompany()) {
            return $this->approval_status === self::APPROVAL_APPROVED;
        }

        return true;
    }

    public function isPendingApproval(): bool
    {
        return ($this->isTalent() || $this->isCompany())
            && $this->approval_status === self::APPROVAL_PENDING;
    }

    public function isRejected(): bool
    {
        return ($this->isTalent() || $this->isCompany())
            && $this->approval_status === self::APPROVAL_REJECTED;
    }

    public function homeRouteName(): string
    {
        if ($this->isRejected()) {
            return 'account.rejected';
        }

        if ($this->isPendingApproval()) {
            return 'account.pending';
        }

        return 'dashboard';
    }

    public function hasActiveSubscription(): bool
    {
        return $this->is_subscribed
            && $this->subscription_expires_at
            && $this->subscription_expires_at->isFuture();
    }

    public function avatarUrl(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        // Relative path so the image works regardless of APP_URL host/port
        // (e.g. browsing via 127.0.0.1:8000 while APP_URL is http://localhost).
        return '/storage/'.ltrim($this->avatar_path, '/');
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/u', trim($this->name)) ?: [];

        if (count($parts) >= 2) {
            return strtoupper(mb_substr($parts[0], 0, 1).mb_substr(end($parts), 0, 1));
        }

        return strtoupper(mb_substr($this->name, 0, 2));
    }

    /**
     * Affichage semi-anonyme : prénom + initiale du nom (ex. « Khalid J. »).
     */
    public function publicDisplayName(): string
    {
        $first = trim((string) ($this->first_name ?: ''));
        $last = trim((string) ($this->last_name ?: ''));

        if ($first === '' || $last === '') {
            $parts = preg_split('/\s+/u', trim($this->name)) ?: [];
            $first = $first !== '' ? $first : ($parts[0] ?? $this->name);
            $last = $last !== '' ? $last : (end($parts) ?: '');
        }

        $initial = $last !== '' ? mb_strtoupper(mb_substr($last, 0, 1)).'.' : '';

        return trim($first.($initial !== '' ? ' '.$initial : ''));
    }

    /**
     * Affichage entreprise : raison sociale pour owner, « Entreprise / Prénom Nom » pour membre.
     */
    public function companyDisplayName(): string
    {
        if (! $this->isCompany()) {
            return $this->name;
        }

        if ($this->isCompanyOwner()) {
            return $this->name;
        }

        $orgName = $this->companyOrganization()?->displayName() ?: $this->name;
        $person = trim(($this->first_name ?? '').' '.($this->last_name ?? ''));

        if ($person === '') {
            return $orgName;
        }

        return $orgName.' / '.$person;
    }
}
