<?php

namespace App\Observers;

use App\Models\Taxonomy;
use Illuminate\Support\Str;

class TaxonomyObserver
{
    public function creating(Taxonomy $taxonomy): void
    {
        // Generate slug if empty
        if (empty($taxonomy->slug)) {
            $taxonomySlug = Str::slug($taxonomy->name);

            $loop = true;
            $slugToCheck = $taxonomySlug;
            $counter = 1;

            while ($loop) {
                if (Taxonomy::where('slug', $slugToCheck)->exists()) {
                    $slugToCheck = $taxonomySlug . '-' . $counter;
                    $counter++;
                }
                else {
                    $taxonomySlug = $slugToCheck;
                    $loop = false;
                }
            }

            $taxonomy->slug = $taxonomySlug;
        }
        // [END] Generate slug if empty
    }

    /**
     * Handle the Taxonomy "created" event.
     */
    public function created(Taxonomy $taxonomy): void
    {
        //
    }

    /**
     * Handle the Taxonomy "updated" event.
     */
    public function updated(Taxonomy $taxonomy): void
    {
        //
    }

    /**
     * Handle the Taxonomy "deleted" event.
     */
    public function deleted(Taxonomy $taxonomy): void
    {
        Taxonomy::where('parent', $taxonomy->id)->delete();
    }

    /**
     * Handle the Taxonomy "restored" event.
     */
    public function restored(Taxonomy $taxonomy): void
    {
        //
    }

    /**
     * Handle the Taxonomy "force deleted" event.
     */
    public function forceDeleted(Taxonomy $taxonomy): void
    {
        //
    }
}
