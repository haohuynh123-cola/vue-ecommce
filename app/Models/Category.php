<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'parent_id',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get the products for the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get all of the images for the category.
     */
    public function images(): MorphToMany
    {
        return $this->morphToMany(Image::class, 'imageable')
            ->withPivot(['type', 'sort_order', 'is_primary'])
            ->withTimestamps();
    }

    /**
     * Get the banner image for the category.
     */
    public function banner(): MorphToMany
    {
        return $this->morphToMany(Image::class, 'imageable')
            ->wherePivot('type', 'banner')
            ->withPivot(['sort_order', 'is_primary'])
            ->withTimestamps();
    }

    /**
     * Get the thumbnail image for the category.
     */
    public function thumbnail(): MorphToMany
    {
        return $this->morphToMany(Image::class, 'imageable')
            ->wherePivot('type', 'thumbnail')
            ->withPivot(['sort_order', 'is_primary'])
            ->withTimestamps();
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include root categories.
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
