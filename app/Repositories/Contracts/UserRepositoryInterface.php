<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find active users
     */
    public function findActiveUsers(): Collection;

    /**
     * Update user last login
     */
    public function updateLastLogin(int $userId): bool;
}
