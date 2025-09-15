<?php

namespace App\Services\Contracts;

use App\Models\User;

interface AuthServiceInterface
{
    /**
     * Register a new user
     */
    public function register(array $data): User;

    /**
     * Login user and return token
     */
    public function login(array $credentials): array;

    /**
     * Logout user
     */
    public function logout(): bool;

    /**
     * Refresh token
     */
    public function refresh(): array;

    /**
     * Get authenticated user
     */
    public function getAuthenticatedUser(): ?User;

    /**
     * Update user profile
     */
    public function updateProfile(array $data): User;
}
