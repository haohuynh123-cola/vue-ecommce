<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find products by category
     */
    public function findByCategory(int $categoryId): Collection;

    /**
     * Find featured products
     */
    public function findFeatured(): Collection;

    /**
     * Find active products
     */
    public function findActive(): Collection;

    /**
     * Search products by name or description
     */
    public function search(string $query): Collection;

    /**
     * Find products in stock
     */
    public function findInStock(): Collection;
}
