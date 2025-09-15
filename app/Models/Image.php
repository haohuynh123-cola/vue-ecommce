<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'original_name',
        'path',
        'url',
        'mime_type',
        'file_size',
        'type',
        'alt_text',
        'description',
        'metadata',
        'is_public',
        'uploaded_by',
        'disk',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'is_public' => 'boolean',
            'file_size' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the user who uploaded this image.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get all of the products that are assigned this image.
     */
    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'imageable')
            ->withPivot(['type', 'sort_order', 'is_primary'])
            ->withTimestamps();
    }

    /**
     * Get all of the categories that are assigned this image.
     */
    public function categories(): MorphToMany
    {
        return $this->morphedByMany(Category::class, 'imageable')
            ->withPivot(['type', 'sort_order', 'is_primary'])
            ->withTimestamps();
    }

    /**
     * Get all of the users that are assigned this image.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'imageable')
            ->withPivot(['type', 'sort_order', 'is_primary'])
            ->withTimestamps();
    }

    /**
     * Get the full URL for the image.
     */
    public function getFullUrlAttribute(): string
    {
        if (filter_var($this->url, FILTER_VALIDATE_URL)) {
            return $this->url;
        }

        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Get the thumbnail URL for the image.
     */
    public function getThumbnailUrlAttribute(): string
    {
        $pathInfo = pathinfo($this->path);
        $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];

        if (Storage::disk($this->disk)->exists($thumbnailPath)) {
            return Storage::disk($this->disk)->url($thumbnailPath);
        }

        return $this->full_url;
    }

    /**
     * Get the medium size URL for the image.
     */
    public function getMediumUrlAttribute(): string
    {
        $pathInfo = pathinfo($this->path);
        $mediumPath = $pathInfo['dirname'] . '/medium/' . $pathInfo['filename'] . '_medium.' . $pathInfo['extension'];

        if (Storage::disk($this->disk)->exists($mediumPath)) {
            return Storage::disk($this->disk)->url($mediumPath);
        }

        return $this->full_url;
    }

    /**
     * Get the large size URL for the image.
     */
    public function getLargeUrlAttribute(): string
    {
        $pathInfo = pathinfo($this->path);
        $largePath = $pathInfo['dirname'] . '/large/' . $pathInfo['filename'] . '_large.' . $pathInfo['extension'];

        if (Storage::disk($this->disk)->exists($largePath)) {
            return Storage::disk($this->disk)->url($largePath);
        }

        return $this->full_url;
    }

    /**
     * Get human readable file size.
     */
    public function getHumanFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get image dimensions from metadata.
     */
    public function getDimensionsAttribute(): ?array
    {
        return $this->metadata['dimensions'] ?? null;
    }

    /**
     * Get image width.
     */
    public function getWidthAttribute(): ?int
    {
        return $this->dimensions['width'] ?? null;
    }

    /**
     * Get image height.
     */
    public function getHeightAttribute(): ?int
    {
        return $this->dimensions['height'] ?? null;
    }

    /**
     * Check if image is a thumbnail.
     */
    public function isThumbnail(): bool
    {
        return $this->type === 'thumbnail';
    }

    /**
     * Check if image is a gallery image.
     */
    public function isGallery(): bool
    {
        return $this->type === 'gallery';
    }

    /**
     * Check if image is a banner.
     */
    public function isBanner(): bool
    {
        return $this->type === 'banner';
    }

    /**
     * Check if image is an avatar.
     */
    public function isAvatar(): bool
    {
        return $this->type === 'avatar';
    }

    /**
     * Scope a query to only include images of a given type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include public images.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include images uploaded by a specific user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('uploaded_by', $userId);
    }

    /**
     * Scope a query to order images by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    /**
     * Delete the image file from storage when model is deleted.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            // Delete the main image file
            if (Storage::disk($image->disk)->exists($image->path)) {
                Storage::disk($image->disk)->delete($image->path);
            }

            // Delete thumbnail if exists
            $pathInfo = pathinfo($image->path);
            $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
            if (Storage::disk($image->disk)->exists($thumbnailPath)) {
                Storage::disk($image->disk)->delete($thumbnailPath);
            }

            // Delete medium size if exists
            $mediumPath = $pathInfo['dirname'] . '/medium/' . $pathInfo['filename'] . '_medium.' . $pathInfo['extension'];
            if (Storage::disk($image->disk)->exists($mediumPath)) {
                Storage::disk($image->disk)->delete($mediumPath);
            }

            // Delete large size if exists
            $largePath = $pathInfo['dirname'] . '/large/' . $pathInfo['filename'] . '_large.' . $pathInfo['extension'];
            if (Storage::disk($image->disk)->exists($largePath)) {
                Storage::disk($image->disk)->delete($largePath);
            }
        });
    }
}
