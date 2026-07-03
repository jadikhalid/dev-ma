<?php

namespace App\Models;

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
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'document_paths' => 'array',
            'expires_at' => 'datetime',
        ];
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
