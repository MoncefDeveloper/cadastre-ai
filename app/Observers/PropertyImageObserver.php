<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\PropertyImage;
use App\Services\Property\PropertyImageService;
use Illuminate\Support\Facades\Log;

class PropertyImageObserver
{
    public bool $afterCommit = true;

    public function __construct(
        protected PropertyImageService $imageService
    ) {}

    /**
     * Handle the PropertyImage "created" event.
     */
    public function created(PropertyImage $propertyImage): void
    {
        //
    }

    /**
     * Handle the PropertyImage "updated" event.
     */
    public function updated(PropertyImage $propertyImage): void
    {
        //
    }

    /**
     * Handle the PropertyImage "deleted" event.
     */
    public function deleted(PropertyImage $propertyImage): void
    {
        Log::info('[PropertyImageObserver] "deleted" event fired!', ['image_id' => $propertyImage->id]);
        $this->imageService->deletePhysicalFile($propertyImage->image_path);
    }
    /**
     * Handle the PropertyImage "restored" event.
     */
    public function restored(PropertyImage $propertyImage): void
    {
        //
    }

    /**
     * Handle the PropertyImage "force deleted" event.
     */
    public function forceDeleted(PropertyImage $propertyImage): void
    {
        //
    }
}
