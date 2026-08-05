<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModeratorPermission extends Model
{
    protected $fillable = [
        'moderator_assignment_id',
        'permission',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(ModeratorAssignment::class, 'moderator_assignment_id');
    }
}
