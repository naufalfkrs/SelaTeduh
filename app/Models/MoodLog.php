<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoodLog extends Model
{
    protected $fillable = ['user_id', 'guest_token', 'mood', 'triggers', 'note', 'logged_date', 'logged_at'];

    protected $hidden = ['guest_token'];

    protected function casts(): array
    {
        return [
            'triggers' => 'array',
            'logged_date' => 'date',
            'logged_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
