<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = ['user_id', 'prompt', 'title', 'content', 'word_count'];
}
