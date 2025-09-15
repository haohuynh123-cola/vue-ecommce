<?php

namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService extends BaseService implements ProductServiceInterface
{
    public function __construct(ProductRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function getFeatured(): Collection
    {
        return $this->repository->findFeatured();
    }

    public function getByCategory(int $categoryId): Collection
    {
        return $this->repository->findByCategory($categoryId);
    }

    public function searchProducts(string $query): Collection
    {
        return $this->repository->search($query);
    }

    public function getWithFilters(array $filters): LengthAwarePaginator
    {
        $query = $this->repository->newQuery();

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        $query->where('is_active', true);

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function updateStock(int $productId, int $quantity): bool
    {
        $product = $this->repository->findOrFail($productId);

        $newStock = $product->stock_quantity + $quantity;
        $inStock = $newStock > 0;

        return $this->repository->update($productId, [
            'stock_quantity' => $newStock,
            'in_stock' => $inStock
        ]);
    }
}