<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'company_user_id',
    'developer_user_id',
    'mode',
    'subject',
    'message',
    'status',
    'admin_comment',
    'status_updated_at',
    'status_updated_by',
    'company_seen_at',
    'staff_seen_at',
    'talent_locked_at',
    'talent_unlocked_at',
])]
class RecruitmentRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED_SUCCESSFUL = 'completed_successful';

    public const STATUS_COMPLETED_UNSUCCESSFUL = 'completed_unsuccessful';

    /** @deprecated Migrated to completed_successful */
    public const STATUS_COMPLETED = 'completed';

    /** @deprecated Migrated to completed_unsuccessful */
    public const STATUS_CANCELLED = 'cancelled';

    /** Intermediation on a pre-chosen talent. */
    public const MODE_NAMED = 'named';

    /** Open sourcing need without a pre-chosen talent. */
    public const MODE_OPEN = 'open';

    /**
     * Company-facing lifecycle: ouvert → en cours → clôturé fructueux / infructueux.
     *
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED_SUCCESSFUL,
            self::STATUS_COMPLETED_UNSUCCESSFUL,
        ];
    }

    /**
     * Statuses that keep the dossier open (ongoing).
     *
     * @return list<string>
     */
    public static function openStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_IN_PROGRESS,
        ];
    }

    /**
     * Statuses that close the dossier lifecycle (chat stays available).
     *
     * @return list<string>
     */
    public static function closedStatuses(): array
    {
        return [
            self::STATUS_COMPLETED_SUCCESSFUL,
            self::STATUS_COMPLETED_UNSUCCESSFUL,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Statuses that keep the dossier open (ongoing) — always block a new named request.
     * Successful closures block only while the company talent lock is active
     * (see hasActiveTalentLock()).
     *
     * @return list<string>
     */
    public static function namedOpenBlockingStatuses(): array
    {
        return self::openStatuses();
    }

    /**
     * @deprecated Prefer namedOpenBlockingStatuses() + hasActiveTalentLock()
     *
     * @return list<string>
     */
    public static function namedBlockingStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED_SUCCESSFUL,
            self::STATUS_COMPLETED,
        ];
    }

    /**
     * @return list<string>
     */
    public static function modes(): array
    {
        return [
            self::MODE_NAMED,
            self::MODE_OPEN,
        ];
    }

    protected function casts(): array
    {
        return [
            'status_updated_at' => 'datetime',
            'company_seen_at' => 'datetime',
            'staff_seen_at' => 'datetime',
            'talent_locked_at' => 'datetime',
            'talent_unlocked_at' => 'datetime',
        ];
    }

    public function hasUnseenChangesForCompany(): bool
    {
        if ($this->company_seen_at === null) {
            return true;
        }

        if ($this->updated_at === null) {
            return false;
        }

        return $this->company_seen_at->lt($this->updated_at);
    }

    public function hasUnseenChangesForStaff(): bool
    {
        if ($this->staff_seen_at === null) {
            return true;
        }

        if ($this->updated_at === null) {
            return false;
        }

        return $this->staff_seen_at->lt($this->updated_at);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(User::class, 'company_user_id');
    }

    public function talent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'developer_user_id');
    }

    public function statusUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'status_updated_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(RecruitmentRequestMessage::class)->orderBy('created_at');
    }

    public function statusEvents(): HasMany
    {
        return $this->hasMany(RecruitmentRequestStatusEvent::class)->orderBy('created_at')->orderBy('id');
    }

    /** @deprecated Use talent() */
    public function developer(): BelongsTo
    {
        return $this->talent();
    }

    public function isNamed(): bool
    {
        return $this->mode === self::MODE_NAMED;
    }

    public function isOpenMode(): bool
    {
        return $this->mode === self::MODE_OPEN;
    }

    /** @deprecated Use isOpenMode() — conflicts with "open status" wording */
    public function isOpen(): bool
    {
        return $this->isOpenMode();
    }

    /**
     * Statuses staff may select on the dossier form (forward-only).
     *
     * @return list<string>
     */
    public function editableStatuses(): array
    {
        return match ($this->normalizeStatus()) {
            self::STATUS_PENDING => self::statuses(),
            self::STATUS_IN_PROGRESS => [
                self::STATUS_COMPLETED_SUCCESSFUL,
                self::STATUS_COMPLETED_UNSUCCESSFUL,
            ],
            default => [],
        };
    }

    public function canTransitionTo(string $status): bool
    {
        $status = $this->normalizeStatus($status);
        $current = $this->normalizeStatus();

        if ($status === $current) {
            return true;
        }

        return in_array($status, $this->editableStatuses(), true);
    }

    public function isClosed(): bool
    {
        return in_array($this->status, self::closedStatuses(), true);
    }

    public function isClosedSuccessful(): bool
    {
        return in_array($this->status, [
            self::STATUS_COMPLETED_SUCCESSFUL,
            self::STATUS_COMPLETED,
        ], true);
    }

    public function isClosedUnsuccessful(): bool
    {
        return in_array($this->status, [
            self::STATUS_COMPLETED_UNSUCCESSFUL,
            self::STATUS_CANCELLED,
        ], true);
    }

    public function hasActiveTalentLock(): bool
    {
        return $this->talent_locked_at !== null && $this->talent_unlocked_at === null;
    }

    public function activateTalentLock(): void
    {
        $this->forceFill([
            'talent_locked_at' => $this->talent_locked_at ?? now(),
            'talent_unlocked_at' => null,
        ])->save();
    }

    public function releaseTalentLock(): void
    {
        if (! $this->hasActiveTalentLock()) {
            return;
        }

        $this->forceFill([
            'talent_unlocked_at' => now(),
        ])->save();
    }

    public function allowsChat(): bool
    {
        return true;
    }

    public function displayTitle(): string
    {
        if ($this->isNamed() && filled($this->talent?->name)) {
            return __('talenma.dashboard.company.sourcing_named_title', [
                'name' => $this->talent->name,
            ]);
        }

        return __('talenma.dashboard.company.sourcing_open_title', [
            'title' => \Illuminate\Support\Str::limit((string) ($this->subject ?: '—'), 80),
        ]);
    }

    public function companyDisplayName(): string
    {
        $company = $this->company;

        if (! $company) {
            return __('talenma.recruitment.party_deleted');
        }

        if ($company->isCompanyOwner()) {
            return $company->name;
        }

        return $company->companyOrganization()?->displayName() ?: $company->name;
    }

    /**
     * Person to name in email bodies (representative or company user).
     */
    public function companyPersonDisplayName(): string
    {
        $this->loadMissing('company');

        if (! $this->company) {
            return __('talenma.recruitment.party_deleted');
        }

        return $this->company->companyMailPersonName();
    }

    public function canAccess(User $user): bool
    {
        if ($user->isStaff()) {
            return true;
        }

        return $user->isCompany()
            && (int) $user->id === (int) $this->company_user_id;
    }

    public function modeLabel(): string
    {
        return __('talenma.recruitment.mode_'.$this->mode);
    }

    public function modeTone(): string
    {
        return match ($this->mode) {
            self::MODE_NAMED => 'violet',
            self::MODE_OPEN => 'indigo',
            default => 'slate',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_COMPLETED => __('talenma.recruitment.status_completed_successful'),
            self::STATUS_CANCELLED => __('talenma.recruitment.status_completed_unsuccessful'),
            default => __('talenma.recruitment.status_'.$this->status),
        };
    }

    public function statusTone(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'sky',
            self::STATUS_IN_PROGRESS => 'amber',
            self::STATUS_COMPLETED_SUCCESSFUL, self::STATUS_COMPLETED => 'emerald',
            self::STATUS_COMPLETED_UNSUCCESSFUL, self::STATUS_CANCELLED => 'rose',
            default => 'slate',
        };
    }

    public function normalizeStatus(?string $status = null): string
    {
        $status ??= $this->status;

        return match ($status) {
            self::STATUS_COMPLETED => self::STATUS_COMPLETED_SUCCESSFUL,
            self::STATUS_CANCELLED => self::STATUS_COMPLETED_UNSUCCESSFUL,
            default => (string) $status,
        };
    }
}
