<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PendingRegistration extends Model
{
    protected $fillable = [
        'token',
        'email',
        'locale',
        'payload',
        'document_paths',
        'expires_at',
        'completed_at',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'document_paths' => 'array',
            'expires_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Rows still waiting for the user to confirm their email.
     * Completed rows are kept briefly for idempotent confirmation links.
     */
    public function scopeAwaitingEmailConfirmation(Builder $query): Builder
    {
        return $query->whereNull('completed_at');
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null && $this->user_id !== null;
    }

    public function isCompanyRegistration(): bool
    {
        return ($this->payload['role'] ?? null) === 'company';
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function greetingName(): string
    {
        $payload = $this->payload ?? [];

        if (($payload['role'] ?? '') === 'company') {
            return (string) ($payload['representative_name'] ?? $payload['name'] ?? '');
        }

        return (string) ($payload['name'] ?? '');
    }

    public function expectsStaffReview(): bool
    {
        $role = $this->payload['role'] ?? null;

        if ($role === 'company') {
            return empty($this->payload['company_trial']);
        }

        if ($role === 'dev') {
            return PlatformSetting::requiresTalentAdminValidation();
        }

        return false;
    }
}
