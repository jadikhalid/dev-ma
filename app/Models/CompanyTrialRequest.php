<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'company_name',
    'contact_name',
    'email',
    'phone',
    'sector',
    'company_description',
    'company_website',
    'company_country',
    'status',
    'rejection_reason',
    'reviewed_by',
    'reviewed_at',
    'user_id',
    'locale',
    'ip_address',
])]
class CompanyTrialRequest extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PROVISIONED = 'provisioned';

    public const STATUS_REJECTED = 'rejected';

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
