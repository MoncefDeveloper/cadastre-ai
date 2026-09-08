<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Property;
use Illuminate\Support\Facades\Log;

class PropertyObserver
{
    /**
     * Handle the Property "created" event.
     */
    public function created(Property $property): void
    {
        //
    }

    /**
     * Handle the Property "updated" event.
     */
    public function updated(Property $property): void
    {
        //
    }

    /**
     * Handle the Property "deleted" event.
     */
    public function deleting(Property $property): void
    {
        Log::info('[PropertyObserver] "deleting" event fired!', ['property_id' => $property->id]);

        $count = $property->images()->count();
        Log::info('[PropertyObserver] Triggering delete on child images.', ['count' => $count]);
        // We trigger the delete on each image model individually so the PropertyImageObserver catches it.
        $property->images->each->delete();
    }

    /**
     * Handle the Property "restored" event.
     */
    public function restored(Property $property): void
    {
        //
    }

    /**
     * Handle the Property "force deleted" event.
     */
    public function forceDeleted(Property $property): void
    {
        //
    }
}
