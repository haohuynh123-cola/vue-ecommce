<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

interface BaseRepositoryInterface
{
    /**
     * Get all records
     */
    public function all(): Collection;

    /**
     * Find record by ID
     */
    public function find(int $id): ?Model;

    /**
     * Find record by ID or fail
     */
    public function findOrFail(int $id): Model;

    /**
     * Create new record
     */
    public function create(array $data): Model;

    /**
     * Update record by ID
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete record by ID
     */
    public function delete(int $id): bool;

    /**
     * Get paginated records
     */
    public function paginate(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    /**
     * Find records by criteria
     */
    public function findBy(array $criteria): Collection;

    /**
     * Find first record by criteria
     */
    public function findFirstBy(array $criteria): ?Model;

    /**
     * Get query
     */
    public function newQuery(): Builder;
}