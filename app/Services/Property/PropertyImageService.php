<?php

declare(strict_types=1);

namespace App\Services\Property;

use App\Models\Property;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PropertyImageService
{
    public function deletePhysicalFile(?string $path): void
    {
        Log::info('[PropertyImageService] Attempting to delete file.', ['path' => $path]);

        if (!$path) {
            Log::warning('[PropertyImageService] Aborted: Path is null.');
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            Log::info('[PropertyImageService] SUCCESS: File deleted from disk.', ['path' => $path]);
        } else {
            Log::error('[PropertyImageService] FAILED: File does not exist on disk.', [
                'path_checked' => Storage::disk('public')->path($path)
            ]);
        }
    }

    public function syncImages(Property $property, array $imagePaths): void
    {
        Log::info('[PropertyImageService] Starting Sync.', ['incoming_paths' => $imagePaths]);

        // 1. Fetch orphaned image models and delete them
        $orphaned = $property->images()->whereNotIn('image_path', $imagePaths)->get();
        Log::info('[PropertyImageService] Found orphaned images to delete.', ['count' => $orphaned->count()]);

        $orphaned->each->delete();

        // 2. Sync remaining/new images
        foreach (array_values($imagePaths) as $index => $path) {
            $property->images()->updateOrCreate(
                ['image_path' => $path],
                ['sort_order' => $index]
            );
        }
    }
}
