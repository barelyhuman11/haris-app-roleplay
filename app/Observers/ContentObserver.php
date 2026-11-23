<?php

namespace App\Observers;

use App\Models\Content;
use Illuminate\Support\Str;

class ContentObserver
{
    public function creating(Content $content): void
    {
        // Generate slug if empty
        if (empty($content->slug)) {
            $contentSlug = Str::slug($content->title);
            $contentSlug = substr($contentSlug, 0, 200);

            $loop = true;
            $slugToCheck = $contentSlug;
            $counter = 1;

            while ($loop) {
                if (Content::where('slug', $slugToCheck)->exists()) {
                    $slugToCheck = $contentSlug . '-' . $counter;
                    $counter++;
                }
                else {
                    $contentSlug = $slugToCheck;
                    $loop = false;
                }
            }

            $content->slug = $contentSlug;
        }
        // [END] Generate slug if empty

        // Default comment status
        if (empty($content->comment_status)) {
            $content->comment_status = 'CLOSED';
        }
        // [END] Default comment status

        // Default content status
        if (empty($content->status)) {
            $content->status = 'PUBLISH';
        }
        // [END] Default content status

        // Default publish time
        if (empty($content->publish_time)) {
            $content->publish_time = time();
        }
        // [END] Default publish time
    }

    /**
     * Handle the Content "created" event.
     */
    public function created(Content $content): void
    {
        //
    }

    /**
     * Handle the Content "updated" event.
     */
    public function updated(Content $content): void
    {
        //
    }

    /**
     * Handle the Content "deleted" event.
     */
    public function deleted(Content $content): void
    {
        Content::where('parent', $content->id)->delete();
    }

    /**
     * Handle the Content "restored" event.
     */
    public function restored(Content $content): void
    {
        //
    }

    /**
     * Handle the Content "force deleted" event.
     */
    public function forceDeleted(Content $content): void
    {
        //
    }
}
