<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CompanyProfileDocument extends Model
{
    protected $fillable = [
        'company_profile_id',
        'path',
        'original_name',
        'mime_type',
        'size',
        'sort_order',
    ];

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function url(): string
    {
        return route('admin.company-profile-documents.show', $this);
    }

    public function formattedSize(): string
    {
        if ($this->size >= 1024 * 1024) {
            return number_format($this->size / (1024 * 1024), 1).' Mo';
        }

        return number_format($this->size / 1024, 0).' Ko';
    }
}
