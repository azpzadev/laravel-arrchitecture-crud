<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Eloquent;

use App\Infrastructure\Models\User;
use App\Infrastructure\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent implementation of the User repository.
 *
 * Handles all database operations for User entities including
 * authentication-related queries and token management.
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    protected function resolveModel(): Model
    {
        return new User();
    }

    /**
     * {@inheritdoc}
     */
    public function findByUsername(string $username): ?User
    {
        return $this->model->where('username', $username)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function existsByUsername(string $username): bool
    {
        return $this->model->where('username', $username)->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function existsByEmail(string $email): bool
    {
        return $this->model->where('email', $email)->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveUsers(): array
    {
        return $this->model
            ->where('is_active', true)
            ->get()
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTokensByDevice(User $user, string $deviceName): int
    {
        return $user->tokens()->where('name', $deviceName)->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAllTokens(User $user): int
    {
        return $user->tokens()->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCurrentToken(User $user): bool
    {
        $token = $user->currentAccessToken();

        if ($token) {
            return (bool) $token->delete();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function createToken(User $user, string $deviceName): string
    {
        $token = $user->createToken($deviceName);

        return $token->plainTextToken;
    }

    /**
     * Apply filters to the user query.
     *
     * @param Builder $query The query builder
     * @param array $filters The filters to apply
     * @return Builder The filtered query
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('username', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['sort_by'])) {
            $direction = $filters['sort_direction'] ?? 'asc';
            $query->orderBy($filters['sort_by'], $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }
}
