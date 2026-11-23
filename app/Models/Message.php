<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'type',
        'message_id',
        'stats_message_id',
        'deepseek_id',
        'message',
        'witnesses',
    ];

    protected $casts = [
        'witnesses' => 'array',
    ];

    protected $attributes = [
        'witnesses' => '[]',
    ];

    /**
     * Filter messages to those with empty witnesses or at least one active character.
     */
    public function scopeForActiveCharacters(Builder $query, array $activeCharacters): Builder
    {
        return $query->where(function (Builder $q) use ($activeCharacters) {
            $q->whereJsonLength('witnesses', 0)
                ->orWhereNull('witnesses');

            foreach ($activeCharacters as $character) {
                $q->orWhereJsonContains('witnesses', $character);
            }
        });
    }
}

