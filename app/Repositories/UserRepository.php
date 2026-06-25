<?php

namespace App\Repositories;

use App\DTO\UserData;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function createFromDto(\App\DTO\DataTransferObject $dto): User
    {
        /** @var User $user */
        $user = parent::createFromDto($dto);

        return $user;
    }

    public function updateFromDto(int|string $id, \App\DTO\DataTransferObject $dto): bool
    {
        return parent::updateFromDto($id, $dto);
    }

    public function updateLocale(User $user, string $locale): bool
    {
        return $this->updateFromDto($user->id, UserData::fromArray(['locale' => $locale]));
    }

    public function updatePassword(User $user, string $password): bool
    {
        return $this->updateFromDto($user->id, UserData::fromArray(['password' => Hash::make($password)]));
    }

    public function regenerateApiToken(User $user): bool
    {
        return $this->updateFromDto($user->id, UserData::fromArray(['api_token' => Str::random(60)]));
    }

    public function forceDelete(User $user): bool
    {
        return (bool) $user->forceDelete();
    }

    public function softDelete(User $user): bool
    {
        return (bool) $user->delete();
    }

    public function restore(User $user): bool
    {
        return (bool) $user->restore();
    }

    public function recentWithTrashed(int $limit): Collection
    {
        return $this->model->newQuery()
            ->withTrashed()
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    public function withTrashedCount(): int
    {
        return $this->model->newQuery()->withTrashed()->count();
    }

    public function withTrashedFindOrFail(int|string $id): User
    {
        return $this->model->newQuery()->withTrashed()->findOrFail($id);
    }

    public function findByEmailOrFail(string $email): User
    {
        return $this->query()
            ->where('email', $email)
            ->firstOrFail();
    }

    /**
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
