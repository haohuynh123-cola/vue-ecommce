<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function findActiveUsers(): Collection
    {
        return $this->model->where('is_active', true)->get();
    }

    public function updateLastLogin(int $userId): bool
    {
        return $this->model->where('id', $userId)->update([
            'last_login_at' => now()
        ]);
    }
}
