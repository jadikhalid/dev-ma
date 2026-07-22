<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileDocumentDownload extends Model
{
    protected $fillable = [
        'profile_document_id',
        'talent_user_id',
        'downloader_user_id',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(ProfileDocument::class, 'profile_document_id');
    }

    public function talent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'talent_user_id');
    }

    public function downloader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'downloader_user_id');
    }
}
