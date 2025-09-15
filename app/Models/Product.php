<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Product extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'sale_price',
        'sku',
        'stock_quantity',
        'manage_stock',
        'in_stock',
        'weight',
        'dimensions',
        'images',
        'attributes',
        'is_active',
        'is_featured',
        'category_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'weight' => 'decimal:2',
            'stock_quantity' => 'integer',
            'manage_stock' => 'boolean',
            'in_stock' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'images' => 'array',
            'attributes' => 'array',
        ];
    }

    /**
     * Audit configuration
     */
    protected $auditInclude = [
        'name',
        'slug',
        'description',
        'price',
        'sale_price',
        'sku',
        'stock_quantity',
        'in_stock',
        'is_active',
        'is_featured',
        'category_id',
    ];

    protected $auditExclude = [
        'updated_at',
        'created_at',
    ];

    protected $auditEvents = [
        'created',
        'updated',
        'deleted',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get all of the images for the product.
     */
    public function images(): MorphToMany
    {
        return $this->morphToMany(Image::class, 'imageable')
            ->withPivot(['type', 'sort_order', 'is_primary'])
            ->withTimestamps();
    }

    /**
     * Get the thumbnail image for the product.
     */
    public function thumbnail(): MorphToMany
    {
        return $this->morphToMany(Image::class, 'imageable')
            ->wherePivot('type', 'thumbnail')
            ->withPivot(['sort_order', 'is_primary'])
            ->withTimestamps();
    }

    /**
     * Get the gallery images for the product.
     */
    public function gallery(): MorphToMany
    {
        return $this->morphToMany(Image::class, 'imageable')
            ->wherePivot('type', 'gallery')
            ->withPivot(['sort_order', 'is_primary'])
            ->withTimestamps()
            ->orderBy('imageables.sort_order');
    }

    /**
     * Get the primary image for the product.
     */
    public function primaryImage(): MorphToMany
    {
        return $this->morphToMany(Image::class, 'imageable')
            ->wherePivot('is_primary', true)
            ->withPivot(['type', 'sort_order'])
            ->withTimestamps();
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include in-stock products.
     */
    public function scopeInStock($query)
    {
        return $query->where('in_stock', true);
    }

    /**
     * Get the current price (sale price if available, otherwise regular price).
     */
    public function getCurrentPriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    /**
     * Check if product is on sale.
     */
    public function getIsOnSaleAttribute(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    /**
     * Get the main image URL for the product.
     */
    public function getMainImageUrlAttribute(): ?string
    {
        $primaryImage = $this->primaryImage()->first();
        if ($primaryImage) {
            return $primaryImage->full_url;
        }

        $thumbnail = $this->thumbnail()->first();
        if ($thumbnail) {
            return $thumbnail->full_url;
        }

        $galleryImage = $this->gallery()->first();
        if ($galleryImage) {
            return $galleryImage->full_url;
        }

        return null;
    }

    /**
     * Get the thumbnail URL for the product.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        $thumbnail = $this->thumbnail()->first();
        if ($thumbnail) {
            return $thumbnail->thumbnail_url;
        }

        $primaryImage = $this->primaryImage()->first();
        if ($primaryImage) {
            return $primaryImage->thumbnail_url;
        }

        return $this->main_image_url;
    }

    /**
     * Get all gallery image URLs.
     */
    public function getGalleryUrlsAttribute(): array
    {
        return $this->gallery()->get()->map(function ($image) {
            return [
                'id' => $image->id,
                'url' => $image->full_url,
                'thumbnail_url' => $image->thumbnail_url,
                'medium_url' => $image->medium_url,
                'large_url' => $image->large_url,
                'alt_text' => $image->alt_text,
                'is_primary' => $image->pivot->is_primary,
                'sort_order' => $image->pivot->sort_order,
            ];
        })->toArray();
    }
}
