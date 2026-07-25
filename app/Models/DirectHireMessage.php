<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'direct_hire_request_id',
    'sender_user_id',
    'body',
    'is_system',
])]
class DirectHireMessage extends Model
{
    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(DirectHireRequest::class, 'direct_hire_request_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }
}
