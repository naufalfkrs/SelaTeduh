<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntry extends Model
{
    protected $fillable = ['user_id', 'guest_token', 'prompt', 'title', 'content', 'word_count'];

    protected $hidden = ['guest_token'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
