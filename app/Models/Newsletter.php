<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'title',
    'subject',
    'locale',
    'audience',
    'status',
    'body_blocks',
    'scheduled_at',
    'sent_at',
    'recipient_count',
    'created_by',
])]
class Newsletter extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_SENDING = 'sending';

    public const STATUS_SENT = 'sent';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_SCHEDULED,
        self::STATUS_SENDING,
        self::STATUS_SENT,
        self::STATUS_CANCELLED,
    ];

    public const LOCALE_FR = 'fr';

    public const LOCALE_EN = 'en';

    public const AUDIENCE_ALL = 'all';

    public const AUDIENCE_REGISTERED = 'registered';

    public const AUDIENCE_GUESTS = 'guests';

    public const AUDIENCES = [
        self::AUDIENCE_ALL,
        self::AUDIENCE_REGISTERED,
        self::AUDIENCE_GUESTS,
    ];

    public const BLOCK_HEADER = 'header';

    public const BLOCK_HERO = 'hero';

    public const BLOCK_JOBS = 'jobs';

    public const BLOCK_BLOG = 'blog';

    public const BLOCK_SOCIAL = 'social';

    public const BLOCK_TALENTS = 'talents';

    public const BLOCK_COMPANIES = 'companies';

    public const BLOCK_STATS = 'stats';

    public const BLOCK_TEXT = 'text';

    public const BLOCK_CTA = 'cta';

    public const BLOCK_REGISTER = 'register';

    public const BLOCK_LIBRARY = 'library';

    public const LIBRARY_LATEST_LIMIT = 10;

    public const BLOCK_CV_TEMPLATES = 'cv_templates';

    public const BLOCK_TYPES = [
        self::BLOCK_HEADER,
        self::BLOCK_HERO,
        self::BLOCK_JOBS,
        self::BLOCK_BLOG,
        self::BLOCK_SOCIAL,
        self::BLOCK_TALENTS,
        self::BLOCK_COMPANIES,
        self::BLOCK_STATS,
        self::BLOCK_TEXT,
        self::BLOCK_CTA,
        self::BLOCK_REGISTER,
        self::BLOCK_LIBRARY,
        self::BLOCK_CV_TEMPLATES,
    ];

    protected function casts(): array
    {
        return [
            'body_blocks' => 'array',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function outbox(): HasMany
    {
        return $this->hasMany(NewsletterOutbox::class);
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SCHEDULED, self::STATUS_CANCELLED], true);
    }

    public function isCancellable(): bool
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    public function statusLabel(): string
    {
        return __('talenma.newsletter.status_'.$this->status);
    }

    public function audienceLabel(): string
    {
        $audience = in_array($this->audience, self::AUDIENCES, true)
            ? $this->audience
            : self::AUDIENCE_ALL;

        return __('talenma.newsletter.audience_'.$audience);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function normalizedBlocks(): array
    {
        $blocks = $this->body_blocks;

        if (! is_array($blocks)) {
            return [];
        }

        return array_values(array_filter(
            $blocks,
            fn ($block) => is_array($block) && filled($block['type'] ?? null)
        ));
    }
}
