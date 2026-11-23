<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Content extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopeType(Builder $builder, $type): void
    {
        $builder->where('type', $type);
    }

    public function contentMeta(): HasMany
    {
        return $this->hasMany(ContentMeta::class);
    }

    public function taxonomies(): BelongsToMany
    {
        return $this->belongsToMany(Taxonomy::class, 'content_taxonomy', 'content_id', 'taxonomy_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMeta($key = '', $defaultValue = null)
    {
        $contentMeta = $this->contentMeta;
        $contentMeta = collect($contentMeta)->first(function ($meta) use ($key) {
                return $meta['key'] == $key;
            });
        $contentMeta = $contentMeta ? $contentMeta['value'] : $defaultValue;
        return $contentMeta;
    }

    public function getTaxonomy($type = '', $defaultValue = null)
    {
        $taxonomies = $this->taxonomies;
        $taxonomies = collect($taxonomies)->first(function ($taxonomy) use ($type) {
            return $taxonomy['type'] == $type;
        });
        $taxonomy = $taxonomies ? $taxonomies : $defaultValue;
        return $taxonomy;
    }
}
