<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BreathingLog extends Model
{
    protected $fillable = ['user_id', 'guest_token', 'rhythm_name', 'cycles', 'duration'];

    protected $hidden = ['guest_token'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
