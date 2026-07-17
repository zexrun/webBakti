<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'admin_id',
        'title',
        'content',
        'priority',
        'target_roles',
        'published_at',
    ];

    protected $casts = [
        'target_roles' => 'array',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function publish()
    {
        if (!$this->published_at) {
            $this->update(['published_at' => now()]);
        }
    }
}
