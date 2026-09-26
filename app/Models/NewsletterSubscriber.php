<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'email',
    'unsubscribe_token',
    'source',
    'subscribed_at',
    'unsubscribed_at',
    'user_id',
    'created_by',
])]
class NewsletterSubscriber extends Model
{
    public const SOURCE_PUBLIC = 'public';

    public const SOURCE_ADMIN = 'admin';

    public const SOURCE_ACCOUNT = 'account';

    public const SOURCE_TALENT = 'talent';

    public const FILTER_ALL = 'all';

    public const FILTER_REGISTERED = 'registered';

    public const FILTER_GUESTS = 'guests';

    public const FILTERS = [
        self::FILTER_ALL,
        self::FILTER_REGISTERED,
        self::FILTER_GUESTS,
    ];

    protected function casts(): array
    {
        return [
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('unsubscribed_at')->whereNotNull('subscribed_at');
    }

    /**
     * Linked to a platform account (user_id or matching users.email).
     */
    public function scopeRegistered(Builder $query): Builder
    {
        return $query->where(function (Builder $inner): void {
            $inner->whereNotNull('user_id')
                ->orWhereExists(function ($sub): void {
                    $sub->selectRaw('1')
                        ->from('users')
                        ->whereRaw('lower(users.email) = lower(newsletter_subscribers.email)');
                });
        });
    }

    /**
     * Open-list email with no matching platform account.
     */
    public function scopeGuests(Builder $query): Builder
    {
        return $query->whereNull('user_id')
            ->whereNotExists(function ($sub): void {
                $sub->selectRaw('1')
                    ->from('users')
                    ->whereRaw('lower(users.email) = lower(newsletter_subscribers.email)');
            });
    }

    public function isActive(): bool
    {
        return $this->subscribed_at !== null && $this->unsubscribed_at === null;
    }

    public function isRegistered(): bool
    {
        if ($this->user_id !== null) {
            return true;
        }

        if ($this->relationLoaded('user') && $this->user !== null) {
            return true;
        }

        return User::query()
            ->whereRaw('lower(email) = ?', [mb_strtolower((string) $this->email)])
            ->exists();
    }

    public function ensureUnsubscribeToken(): string
    {
        if (filled($this->unsubscribe_token)) {
            return (string) $this->unsubscribe_token;
        }

        $token = bin2hex(random_bytes(32));
        $this->forceFill(['unsubscribe_token' => $token])->save();

        return $token;
    }

    public function sourceLabel(): string
    {
        return __('talenma.newsletter.subscriber_source_'.$this->source);
    }
}
