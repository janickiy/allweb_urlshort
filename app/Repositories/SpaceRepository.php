<?php

namespace App\Repositories;

use App\Models\Space;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SpaceRepository extends BaseRepository
{
    public function __construct(Space $model)
    {
        parent::__construct($model);
    }

    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function forUser(int $userId): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->get();
    }

    public function paginateForUser(int $userId, ?string $search, string $sort = 'desc', int $perPage = 10): LengthAwarePaginator
    {
        return $this->query()
            ->where('user_id', $userId)
            ->when($search, fn (Builder $query) => $query->searchName($search))
            ->orderBy('id', $sort === 'asc' ? 'asc' : 'desc')
            ->paginate($perPage)
            ->appends(['search' => $search, 'sort' => $sort]);
    }

    public function findForUserOrFail(int|string $id, int $userId): Space
    {
        return $this->query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function maxId(): int
    {
        return (int) $this->query()->max('id');
    }

    public function countForUser(int $userId): int
    {
        return $this->query()->where('user_id', $userId)->count();
    }

    public function paginateForAdmin(?int $userId, ?string $search, string $sort = 'desc', int $perPage = 10): LengthAwarePaginator
    {
        return $this->query()
            ->when($search, fn (Builder $query) => $query->searchName($search))
            ->when($userId, fn (Builder $query) => $query->userId($userId))
            ->orderBy('id', $sort === 'asc' ? 'asc' : 'desc')
            ->paginate($perPage)
            ->appends(['search' => $search, 'sort' => $sort, 'user_id' => $userId]);
    }
}
