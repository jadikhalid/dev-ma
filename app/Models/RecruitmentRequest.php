<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'company_user_id',
    'developer_user_id',
    'mode',
    'subject',
    'message',
    'status',
])]
class RecruitmentRequest extends Model
{
    use HasFactory;

    public function company(): BelongsTo
    {
        return $this->belongsTo(User::class, 'company_user_id');
    }

    public function talent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'developer_user_id');
    }

    /** @deprecated Use talent() */
    public function developer(): BelongsTo
    {
        return $this->talent();
    }
}
