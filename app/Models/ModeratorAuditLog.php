<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModeratorAuditLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'moderator_user_id',
        'moderator_assignment_id',
        'moderator_name_snapshot',
        'moderator_email_snapshot',
        'action',
        'permissions_snapshot',
        'context',
        'performed_by',
    ];

    protected function casts(): array
    {
        return [
            'permissions_snapshot' => 'array',
            'context' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_user_id');
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(ModeratorAssignment::class, 'moderator_assignment_id');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
