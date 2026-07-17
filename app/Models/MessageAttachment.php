<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'message_id',
    'disk',
    'path',
    'original_name',
    'mime_type',
    'size',
])]
class MessageAttachment extends Model
{
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function formattedSize(): string
    {
        if ($this->size >= 1024 * 1024) {
            return number_format($this->size / (1024 * 1024), 1).' Mo';
        }

        return number_format($this->size / 1024, 0).' Ko';
    }
}
