<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileView extends Model
{
    protected $fillable = [
        'talent_user_id',
        'viewer_user_id',
    ];

    public function talent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'talent_user_id');
    }

    public function viewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'viewer_user_id');
    }
}
