<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'company_user_id',
    'talent_user_id',
    'subject',
    'last_message_at',
    'company_last_read_at',
    'talent_last_read_at',
])]
class Conversation extends Model
{
    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'company_last_read_at' => 'datetime',
            'talent_last_read_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(User::class, 'company_user_id');
    }

    public function talent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'talent_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function isParticipant(User $user): bool
    {
        return (int) $this->company_user_id === (int) $user->id
            || (int) $this->talent_user_id === (int) $user->id;
    }

    public function counterpartFor(User $user): ?User
    {
        if ((int) $this->company_user_id === (int) $user->id) {
            return $this->talent;
        }

        if ((int) $this->talent_user_id === (int) $user->id) {
            return $this->company;
        }

        return null;
    }

    public function unreadFor(User $user): bool
    {
        if (! $this->last_message_at) {
            return false;
        }

        $lastRead = (int) $this->company_user_id === (int) $user->id
            ? $this->company_last_read_at
            : $this->talent_last_read_at;

        if (! $lastRead) {
            return true;
        }

        return $this->last_message_at->greaterThan($lastRead);
    }

    public function markReadFor(User $user): void
    {
        $column = (int) $this->company_user_id === (int) $user->id
            ? 'company_last_read_at'
            : 'talent_last_read_at';

        $this->forceFill([$column => now()])->save();
    }
}
