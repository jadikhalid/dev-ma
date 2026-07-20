<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProfileDocument extends Model
{
    public const TYPE_REGISTRATION = 'registration';

    public const TYPE_CV = 'cv';

    /** @deprecated Kept for legacy reads; other documents are no longer used. */
    public const TYPE_OTHER = 'other';

    /** @var list<string> */
    public const CV_LANGUAGES = ['fr', 'en', 'ar', 'es'];

    protected $fillable = [
        'profile_id',
        'document_type',
        'language',
        'path',
        'original_name',
        'mime_type',
        'size',
        'sort_order',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('document_type', $type);
    }

    public function isCv(): bool
    {
        return $this->document_type === self::TYPE_CV;
    }

    public function isOther(): bool
    {
        return $this->document_type === self::TYPE_OTHER;
    }

    public function languageLabel(): ?string
    {
        if (! filled($this->language)) {
            return null;
        }

        return __('talenma.talent.lang_'.$this->language);
    }

    public function url(): string
    {
        return route('admin.profile-documents.show', $this);
    }

    public function talentUrl(): string
    {
        return route('profile.documents.show', $this);
    }

    public function publicUrl(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    public function formattedSize(): string
    {
        if ($this->size >= 1024 * 1024) {
            return number_format($this->size / (1024 * 1024), 1).' Mo';
        }

        return number_format($this->size / 1024, 0).' Ko';
    }
}
