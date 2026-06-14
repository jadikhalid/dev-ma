<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'title',
    'bio',
    'experience_years',
    'daily_rate_eur',
    'availability',
    'city',
    'country',
    'skills',
    'github_url',
    'linkedin_url',
    'portfolio_url',
])]
class Profile extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'skills' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
