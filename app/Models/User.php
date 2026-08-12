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
use App\Support\PublicStorageUrl;
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
    'data_processing_consent_at',
    'data_processing_consent_version',
    'approval_status',
    'approved_at',
    'approved_by',
    'rejection_reason',
    'disabled_at',
    'is_subscribed',
    'subscription_expires_at',
    'dashboard_activity_seen_at',
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
            'data_processing_consent_at' => 'datetime',
            'pending_email_expires_at' => 'datetime',
            'approved_at' => 'datetime',
            'disabled_at' => 'datetime',
            'dashboard_activity_seen_at' => 'datetime',
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

    public function moderatorAssignments(): HasMany
    {
        return $this->hasMany(ModeratorAssignment::class);
    }

    public function activeModeratorAssignment(): ?ModeratorAssignment
    {
        $this->loadMissing('moderatorAssignments.permissions');

        return $this->moderatorAssignments
            ->first(fn (ModeratorAssignment $assignment) => $assignment->isActive());
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

    /**
     * Owner + member user IDs attached to the same company organization.
     *
     * @return list<int>
     */
    public function companyTeamUserIds(): array
    {
        if (! $this->isCompany()) {
            return [];
        }

        $org = $this->companyOrganization();

        if (! $org) {
            return [(int) $this->id];
        }

        $ids = [];

        if (filled($org->user_id)) {
            $ids[] = (int) $org->user_id;
        }

        $org->loadMissing('memberships');

        foreach ($org->memberships as $membership) {
            if (filled($membership->user_id)) {
                $ids[] = (int) $membership->user_id;
            }
        }

        return array_values(array_unique($ids));
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

    /**
     * Talent with an active moderator assignment (regardless of UI mode).
     */
    public function isModerator(): bool
    {
        return $this->isTalent() && $this->activeModeratorAssignment() !== null;
    }

    /**
     * Can enter moderator mode (assignment + at least one permission).
     */
    public function canActAsModerator(): bool
    {
        if (! $this->isModerator()) {
            return false;
        }

        return $this->moderatorPermissionKeys() !== [];
    }

    public function isActingAsModerator(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (! $this->canActAsModerator()) {
            return false;
        }

        return (bool) session(\App\Services\ModeratorAssignmentService::SESSION_MODE_KEY, false);
    }

    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->isActingAsModerator();
    }

    /**
     * @return list<string>
     */
    public function moderatorPermissionKeys(): array
    {
        return $this->activeModeratorAssignment()?->permissionKeys() ?? [];
    }

    public function hasModeratorPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (! $this->isActingAsModerator()) {
            return false;
        }

        return in_array($permission, $this->moderatorPermissionKeys(), true);
    }

    public function canAccessStaffMessaging(): bool
    {
        return $this->hasModeratorPermission(ModeratorPermissionCatalog::STAFF_MESSAGES_MANAGE);
    }

    public function isApproved(): bool
    {
        if ($this->isAdmin()) {
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
        // ?v= busts browser cache when the avatar file is overwritten in place.
        return PublicStorageUrl::make($this->avatar_path, $this->updated_at);
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
     * Nom complet : prénom + nom, sinon name.
     */
    public function formalDisplayName(): string
    {
        $person = trim((string) (($this->first_name ?? '').' '.($this->last_name ?? '')));

        if ($person !== '') {
            return $person;
        }

        $name = trim((string) $this->name);

        return $name !== '' ? $name : __('talenma.talent.anonymous');
    }

    /**
     * Nom affiché dans le header (titre de chaque mot).
     */
    public function headerDisplayName(): string
    {
        if ($this->isCompany()) {
            return $this->companyMailPersonName();
        }

        return $this->titleCasePersonName($this->formalDisplayName());
    }

    /**
     * Étiquette de compte : « Raison sociale/Administrateur » côté entreprise.
     */
    public function roleLabel(): string
    {
        if ($this->isCompanyOwner() || $this->isCompanyMember()) {
            $orgName = trim((string) ($this->companyOrganization()?->displayName() ?? ''));
            $seat = $this->isCompanyOwner()
                ? __('talenma.roles.company_seat_owner')
                : __('talenma.roles.company_seat_member');

            return $orgName !== '' ? $orgName.'/'.$seat : $seat;
        }

        return match (true) {
            $this->isAdmin() => __('talenma.roles.admin'),
            $this->isActingAsModerator() => __('talenma.roles.moderator'),
            $this->isTalent() => __('talenma.roles.talent'),
            $this->isCompany() => __('talenma.roles.company'),
            default => '',
        };
    }

    public function roleBadgeClasses(): string
    {
        return match (true) {
            $this->isAdmin() => 'bg-violet-100 text-violet-700',
            $this->isActingAsModerator() => 'bg-purple-100 text-purple-700',
            $this->isTalent() => 'bg-indigo-100 text-indigo-700',
            default => 'bg-emerald-100 text-emerald-700',
        };
    }

    /**
     * Affichage entreprise : « Raison sociale / Prénom Nom » (admin ou utilisateur).
     */
    public function companyDisplayName(): string
    {
        if (! $this->isCompany()) {
            return $this->name;
        }

        $org = $this->companyOrganization();
        $orgName = trim((string) ($org?->displayName() ?: $this->name));

        if ($orgName === '') {
            $orgName = trim((string) $this->name);
        }

        if ($this->isCompanyOwner()) {
            $person = trim((string) ($org?->representative_name ?? ''));

            if ($person === '') {
                $person = trim((string) (($this->first_name ?? '').' '.($this->last_name ?? '')));
            }

            if ($person === '' || strcasecmp($person, $orgName) === 0) {
                return $orgName !== '' ? $orgName : $this->name;
            }

            return $orgName.' / '.$person;
        }

        $person = trim((string) (($this->first_name ?? '').' '.($this->last_name ?? '')));

        if ($person === '' || $orgName === '') {
            return $orgName !== '' ? $orgName : $this->name;
        }

        return $orgName.' / '.$person;
    }

    /**
     * Person name for email bodies: representative, else member/user name (never the org brand alone when a person is known).
     */
    public function companyMailPersonName(): string
    {
        if (! $this->isCompany()) {
            return $this->titleCasePersonName($this->name);
        }

        if ($this->isCompanyMember()) {
            $person = trim((string) (($this->first_name ?? '').' '.($this->last_name ?? '')));

            if ($person === '') {
                $person = trim((string) $this->name);
            }

            if ($person !== '') {
                return $this->titleCasePersonName($person);
            }
        }

        $representative = trim((string) ($this->companyOrganization()?->representative_name ?? ''));

        if ($representative !== '') {
            return $this->titleCasePersonName($representative);
        }

        $person = trim((string) (($this->first_name ?? '').' '.($this->last_name ?? '')));

        if ($person !== '') {
            return $this->titleCasePersonName($person);
        }

        return $this->titleCasePersonName($this->name);
    }

    private function titleCasePersonName(string $name): string
    {
        $normalized = trim(preg_replace('/\s+/u', ' ', $name) ?: '');

        if ($normalized === '') {
            return '';
        }

        return mb_convert_case($normalized, MB_CASE_TITLE, 'UTF-8');
    }
}
