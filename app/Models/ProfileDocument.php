<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProfileDocument extends Model
{
    protected $fillable = [
        'profile_id',
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

    public function url(): string
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
