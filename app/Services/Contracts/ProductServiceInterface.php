<?php

namespace App\Services\Contracts;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductServiceInterface extends BaseServiceInterface
{
    /**
     * Get featured products
     */
    public function getFeatured(): Collection;

    /**
     * Get products by category
     */
    public function getByCategory(int $categoryId): Collection;

    /**
     * Search products
     */
    public function searchProducts(string $query): Collection;

    /**
     * Get products with filters
     */
    public function getWithFilters(array $filters): LengthAwarePaginator;

    /**
     * Update product stock
     */
    public function updateStock(int $productId, int $quantity): bool;
}
