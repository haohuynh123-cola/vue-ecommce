<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find active categories
     */
    public function findActive(): Collection;

    /**
     * Find root categories
     */
    public function findRoot(): Collection;

    /**
     * Find categories by parent
     */
    public function findByParent(int $parentId): Collection;

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug): ?Category;
}
