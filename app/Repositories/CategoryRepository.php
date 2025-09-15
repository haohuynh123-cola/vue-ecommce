<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function findActive(): Collection
    {
        return $this->model->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function findRoot(): Collection
    {
        return $this->model->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function findByParent(int $parentId): Collection
    {
        return $this->model->where('parent_id', $parentId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->model->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }
}
