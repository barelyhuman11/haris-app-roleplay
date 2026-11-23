<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'chat_id',
        'is_auth',
        'state',
        'active_characters',
    ];

    protected $casts = [
        'active_characters' => 'array',
    ];

    protected $attributes = [
        'active_characters' => '[]',
    ];
}
