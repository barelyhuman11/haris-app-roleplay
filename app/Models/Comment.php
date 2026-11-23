<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeType(Builder $builder, $type): void
    {
        $builder->where('type', $type);
    }

    public function commentMeta(): HasMany
    {
        return $this->hasMany(CommentMeta::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMeta($key = '', $defaultValue = null)
    {
        $commentMeta = $this->commentMeta;
        $commentMeta = collect($commentMeta)->first(function ($meta) use ($key) {
                return $meta['key'] == $key;
            });
        $commentMeta = $commentMeta ? $commentMeta['value'] : $defaultValue;
        return $commentMeta;
    }
}
