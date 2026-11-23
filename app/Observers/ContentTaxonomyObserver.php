<?php

namespace App\Observers;

use App\Models\ContentTaxonomy;
use App\Models\Taxonomy;

class ContentTaxonomyObserver
{
    /**
     * Handle the ContentTaxonomy "created" event.
     */
    public function created(ContentTaxonomy $contentTaxonomy): void
    {
        Taxonomy::find($contentTaxonomy->taxonomy_id)
            ->increment('content_count');
    }

    /**
     * Handle the ContentTaxonomy "updated" event.
     */
    public function updated(ContentTaxonomy $contentTaxonomy): void
    {
        Taxonomy::find($contentTaxonomy->getOriginal('taxonomy_id'))
            ->decrement('content_count');

        Taxonomy::find($contentTaxonomy->taxonomy_id)
            ->increment('content_count');
    }

    /**
     * Handle the ContentTaxonomy "deleted" event.
     */
    public function deleted(ContentTaxonomy $contentTaxonomy): void
    {
        Taxonomy::find($contentTaxonomy->taxonomy_id)
            ->decrement('content_count');
    }

    /**
     * Handle the ContentTaxonomy "restored" event.
     */
    public function restored(ContentTaxonomy $contentTaxonomy): void
    {
        //
    }

    /**
     * Handle the ContentTaxonomy "force deleted" event.
     */
    public function forceDeleted(ContentTaxonomy $contentTaxonomy): void
    {
        //
    }
}
