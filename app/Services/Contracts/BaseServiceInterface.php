<?php

namespace App\Services\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

interface BaseServiceInterface
{
    /**
     * Get all records
     */
    public function getAll(): Collection;

    /**
     * Get record by ID
     */
    public function getById(int $id): ?Model;

    /**
     * Create new record
     */
    public function create(array $data): Model;

    /**
     * Update record by ID
     */
    public function update(int $id, array $data): Model;

    /**
     * Delete record by ID
     */
    public function delete(int $id): bool;

    /**
     * Get paginated records
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;

    /**
     * Search records by criteria
     */
    public function search(array $criteria): Collection;

    /**
     * Get query
     */
    public function newQuery(): Builder;
}
