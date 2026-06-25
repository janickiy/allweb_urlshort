<?php

namespace App\Repositories;

use App\DTO\UserData;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository extends BaseRepository
{
    /**
     * Inject the user model used by the repository.
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Persist a new user from a data transfer object.
     */
    public function createFromDto(\App\DTO\DataTransferObject $dto): User
    {
        /** @var User $user */
        $user = parent::createFromDto($dto);

        return $user;
    }

    /**
     * Update a user by primary key with data from a DTO.
     */
    public function updateFromDto(int|string $id, \App\DTO\DataTransferObject $dto): bool
    {
        return parent::updateFromDto($id, $dto);
    }

    /**
     * Update the selected locale for a user.
     */
    public function updateLocale(User $user, string $locale): bool
    {
        return $this->updateFromDto($user->id, UserData::fromArray(['locale' => $locale]));
    }

    /**
     * Update the hashed password for a user.
     */
    public function updatePassword(User $user, string $password): bool
    {
        return $this->updateFromDto($user->id, UserData::fromArray(['password' => Hash::make($password)]));
    }

    /**
     * Generate and store a new API token for a user.
     */
    public function regenerateApiToken(User $user): bool
    {
        return $this->updateFromDto($user->id, UserData::fromArray(['api_token' => Str::random(60)]));
    }

    /**
     * Return the highest user identifier currently stored.
     */
    public function maxId(): int
    {
        return (int) $this->query()->max('id');
    }

    /**
     * Permanently delete a user.
     */
    public function forceDelete(User $user): bool
    {
        return (bool) $user->forceDelete();
    }

    /**
     * Soft-delete a user.
     */
    public function softDelete(User $user): bool
    {
        return (bool) $user->delete();
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore(User $user): bool
    {
        return (bool) $user->restore();
    }

    /**
     * Return the most recent users including trashed records.
     */
    public function recentWithTrashed(int $limit): Collection
    {
        return $this->model->newQuery()
            ->withTrashed()
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Count users including trashed records.
     */
    public function withTrashedCount(): int
    {
        return $this->model->newQuery()->withTrashed()->count();
    }

    /**
     * Find a user including trashed records or throw.
     */
    public function withTrashedFindOrFail(int|string $id): User
    {
        return $this->model->newQuery()->withTrashed()->findOrFail($id);
    }

    /**
     * Find a user by email address or throw.
     */
    public function findByEmailOrFail(string $email)
    {
        return $this->query()
            ->where('email', $email)
            ->firstOrFail();
    }

    /**
     * Paginate users for the admin panel with filters.
     *
     * @param array<string, mixed> $filters
     */
    public function paginateForAdmin(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->withTrashed()
            ->when(isset($filters['role']) && is_numeric($filters['role']), fn ($query) => $query->searchRole($filters['role']))
            ->when($filters['search'] ?? null, function ($query, mixed $search) use ($filters) {
                return ($filters['by'] ?? null) === 'email'
                    ? $query->searchEmail($search)
                    : $query->searchName($search);
            })
            ->orderBy('id', ($filters['sort'] ?? null) === 'asc' ? 'asc' : 'desc')
            ->paginate($perPage)
            ->appends([
                'search' => $filters['search'] ?? null,
                'by' => $filters['by'] ?? null,
                'role' => $filters['role'] ?? null,
                'sort' => $filters['sort'] ?? null,
            ]);
    }
}
