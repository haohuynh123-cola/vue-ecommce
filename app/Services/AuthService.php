<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;

        return $this->userRepository->create($data);
    }

    public function login(array $credentials): array
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            throw new \Exception('Invalid credentials');
        }

        $user = Auth::user();
        $this->userRepository->updateLastLogin($user->id);

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ];
    }

    public function logout(): bool
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return true;
    }

    public function refresh(): array
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());
        $user = Auth::user();

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ];
    }

    public function getAuthenticatedUser(): ?User
    {
        return Auth::user();
    }

    public function updateProfile(array $data): User
    {
        $user = Auth::user();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $this->userRepository->update($user->id, $data);

        return $this->userRepository->findOrFail($user->id);
    }
}
