<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as ImageIntervention;

class ImageService
{
    /**
     * Upload and process an image.
     */
    public function uploadImage(UploadedFile $file, string $type = 'gallery', array $options = []): Image
    {
        $originalName = $file->getClientOriginalName();
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = 'images/' . date('Y/m/d') . '/' . $filename;

        // Store the original image
        $storedPath = $file->storeAs('images/' . date('Y/m/d'), $filename, 'public');

        // Get image dimensions and metadata
        $image = ImageIntervention::make($file);
        $dimensions = [
            'width' => $image->width(),
            'height' => $image->height(),
        ];

        // Create different sizes
        $this->createImageSizes($image, $path);

        // Save image record
        return Image::create([
            'filename' => $filename,
            'original_name' => $originalName,
            'path' => $storedPath,
            'url' => Storage::disk('public')->url($storedPath),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'type' => $type,
            'alt_text' => $options['alt_text'] ?? null,
            'description' => $options['description'] ?? null,
            'metadata' => [
                'dimensions' => $dimensions,
                'exif' => $image->exif(),
            ],
            'is_public' => $options['is_public'] ?? true,
            'uploaded_by' => auth()->id(),
            'disk' => 'public',
            'sort_order' => $options['sort_order'] ?? 0,
        ]);
    }

    /**
     * Attach image to a model.
     */
    public function attachImage(Image $image, $model, string $type = 'gallery', array $options = []): void
    {
        $model->images()->attach($image->id, [
            'type' => $type,
            'is_primary' => $options['is_primary'] ?? false,
            'sort_order' => $options['sort_order'] ?? 0,
        ]);
    }

    /**
     * Detach image from a model.
     */
    public function detachImage(Image $image, $model): void
    {
        $model->images()->detach($image->id);
    }

    /**
     * Set primary image for a model.
     */
    public function setPrimaryImage(Image $image, $model, string $type = 'gallery'): void
    {
        // Remove primary status from other images of the same type
        $model->images()
            ->wherePivot('type', $type)
            ->updateExistingPivot($model->images()->pluck('id'), ['is_primary' => false]);

        // Set this image as primary
        $model->images()->updateExistingPivot($image->id, [
            'is_primary' => true,
            'type' => $type,
        ]);
    }

    /**
     * Reorder images for a model.
     */
    public function reorderImages($model, array $imageIds): void
    {
        foreach ($imageIds as $index => $imageId) {
            $model->images()->updateExistingPivot($imageId, [
                'sort_order' => $index + 1,
            ]);
        }
    }

    /**
     * Get optimized image URL for different sizes.
     */
    public function getOptimizedUrl(Image $image, string $size = 'original'): string
    {
        return match ($size) {
            'thumbnail' => $image->thumbnail_url,
            'medium' => $image->medium_url,
            'large' => $image->large_url,
            default => $image->full_url,
        };
    }

    /**
     * Create different sizes of the image.
     */
    private function createImageSizes($image, string $path): void
    {
        $pathInfo = pathinfo($path);

        // Create thumbnail (300x300)
        $this->createThumbnail($image, $pathInfo);

        // Create medium size (800x600)
        $this->createMediumSize($image, $pathInfo);

        // Create large size (1200x900)
        $this->createLargeSize($image, $pathInfo);
    }

    /**
     * Create thumbnail version.
     */
    private function createThumbnail($image, array $pathInfo): void
    {
        $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];

        $thumbnail = clone $image;
        $thumbnail->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        Storage::disk('public')->put($thumbnailPath, $thumbnail->encode());
    }

    /**
     * Create medium size version.
     */
    private function createMediumSize($image, array $pathInfo): void
    {
        $mediumPath = $pathInfo['dirname'] . '/medium/' . $pathInfo['filename'] . '_medium.' . $pathInfo['extension'];

        $medium = clone $image;
        $medium->resize(800, 600, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        Storage::disk('public')->put($mediumPath, $medium->encode());
    }

    /**
     * Create large size version.
     */
    private function createLargeSize($image, array $pathInfo): void
    {
        $largePath = $pathInfo['dirname'] . '/large/' . $pathInfo['filename'] . '_large.' . $pathInfo['extension'];

        $large = clone $image;
        $large->resize(1200, 900, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        Storage::disk('public')->put($largePath, $large->encode());
    }

    /**
     * Delete image and all its sizes.
     */
    public function deleteImage(Image $image): bool
    {
        try {
            // Delete all size variants
            $this->deleteImageVariants($image);

            // Delete the image record (this will trigger the model's boot method)
            return $image->delete();
        } catch (\Exception $e) {
            \Log::error('Failed to delete image: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete all variants of an image.
     */
    private function deleteImageVariants(Image $image): void
    {
        $pathInfo = pathinfo($image->path);
        $variants = [
            'thumbnails/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'],
            'medium/' . $pathInfo['filename'] . '_medium.' . $pathInfo['extension'],
            'large/' . $pathInfo['filename'] . '_large.' . $pathInfo['extension'],
        ];

        foreach ($variants as $variant) {
            $variantPath = $pathInfo['dirname'] . '/' . $variant;
            if (Storage::disk($image->disk)->exists($variantPath)) {
                Storage::disk($image->disk)->delete($variantPath);
            }
        }
    }

    /**
     * Get images by type for a model.
     */
    public function getImagesByType($model, string $type): \Illuminate\Database\Eloquent\Collection
    {
        return $model->images()
            ->wherePivot('type', $type)
            ->orderBy('imageables.sort_order')
            ->get();
    }

    /**
     * Get primary image for a model.
     */
    public function getPrimaryImage($model, string $type = 'gallery'): ?Image
    {
        return $model->images()
            ->wherePivot('type', $type)
            ->wherePivot('is_primary', true)
            ->first();
    }

    /**
     * Bulk upload images.
     */
    public function bulkUpload(array $files, string $type = 'gallery', array $options = []): array
    {
        $uploadedImages = [];

        foreach ($files as $index => $file) {
            $imageOptions = array_merge($options, [
                'sort_order' => $options['sort_order'] ?? $index + 1,
            ]);

            $uploadedImages[] = $this->uploadImage($file, $type, $imageOptions);
        }

        return $uploadedImages;
    }

    /**
     * Validate image file.
     */
    public function validateImage(UploadedFile $file): array
    {
        $errors = [];

        // Check file size (10MB max)
        if ($file->getSize() > 10 * 1024 * 1024) {
            $errors[] = 'File size must be less than 10MB';
        }

        // Check MIME type
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'File must be a valid image (JPEG, PNG, GIF, or WebP)';
        }

        // Check image dimensions
        try {
            $image = ImageIntervention::make($file);
            if ($image->width() < 100 || $image->height() < 100) {
                $errors[] = 'Image must be at least 100x100 pixels';
            }
            if ($image->width() > 5000 || $image->height() > 5000) {
                $errors[] = 'Image must be less than 5000x5000 pixels';
            }
        } catch (\Exception $e) {
            $errors[] = 'Invalid image file';
        }

        return $errors;
    }
}
