<?php

namespace App\Models;

use App\Support\LibraryBookStorage;
use App\Support\LibraryCoverStorage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryBook extends Model
{
    protected $fillable = [
        'library_category_id',
        'title',
        'author',
        'description',
        'cover_path',
        'file_path',
        'original_filename',
        'mime',
        'size_bytes',
        'download_count',
        'is_published',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'download_count' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (LibraryBook $book) {
            LibraryBookStorage::delete($book->file_path);
            LibraryCoverStorage::delete($book->cover_path);
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LibraryCategory::class, 'library_category_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function coverUrl(): ?string
    {
        return LibraryCoverStorage::url($this->cover_path, $this->updated_at);
    }

    public function formattedSize(): string
    {
        $bytes = max(0, (int) $this->size_bytes);

        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return round($bytes / (1024 * 1024), 1).' MB';
    }
}
