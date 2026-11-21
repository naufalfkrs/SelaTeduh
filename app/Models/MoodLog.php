<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoodLog extends Model
{
    protected $fillable = ['user_id', 'mood', 'triggers', 'note', 'logged_date'];

    protected function casts(): array
    {
        return [
            'triggers' => 'array',
            'logged_date' => 'date',
        ];
    }
}
