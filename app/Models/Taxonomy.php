<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Taxonomy extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopeType(Builder $builder, $type): void
    {
        $builder->where('type', $type);
    }

    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'content_taxonomy', 'taxonomy_id', 'content_id');
    }

    public function parentInstance(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class, 'parent');
    }
}
