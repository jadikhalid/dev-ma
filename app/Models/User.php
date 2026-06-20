<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_subscribed', 'subscription_expires_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_subscribed' => 'boolean',
            'subscription_expires_at' => 'datetime',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function companyProfile(): HasOne
    {
        return $this->hasOne(CompanyProfile::class);
    }

    public function recruitmentRequests(): HasMany
    {
        return $this->hasMany(RecruitmentRequest::class, 'company_user_id');
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

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasActiveSubscription(): bool
    {
        return $this->is_subscribed
            && $this->subscription_expires_at
            && $this->subscription_expires_at->isFuture();
    }
}
