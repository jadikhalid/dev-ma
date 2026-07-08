<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModerationRequest extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const ACTION_APPROVE_TALENT = 'approve_talent';

    public const ACTION_REJECT_TALENT = 'reject_talent';

    public const ACTION_APPROVE_COMPANY = 'approve_company';

    public const ACTION_REJECT_COMPANY = 'reject_company';

    public const ACTION_DELETE_USER = 'delete_user';

    public const ACTION_CREATE_USER = 'create_user';

    public const ACTION_GRANT_MODERATOR = 'grant_moderator';

    public const ACTION_REVOKE_MODERATOR = 'revoke_moderator';

    protected $fillable = [
        'requested_by',
        'action_type',
        'target_user_id',
        'payload',
        'status',
        'reviewed_by',
        'reviewed_at',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
