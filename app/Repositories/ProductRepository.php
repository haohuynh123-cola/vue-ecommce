<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function findByCategory(int $categoryId): Collection
    {
        return $this->model->where('category_id', $categoryId)
            ->where('is_active', true)
            ->get();
    }

    public function findFeatured(): Collection
    {
        return $this->model->where('is_featured', true)
            ->where('is_active', true)
            ->get();
    }

    public function findActive(): Collection
    {
        return $this->model->where('is_active', true)->get();
    }

    public function search(string $query): Collection
    {
        return $this->model->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('short_description', 'like', "%{$query}%");
            })
            ->get();
    }

    public function findInStock(): Collection
    {
        return $this->model->where('is_active', true)
            ->where('in_stock', true)
            ->get();
    }
}
