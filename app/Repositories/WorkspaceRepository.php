<?php

namespace App\Repositories;

use App\Models\Workspace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class WorkspaceRepository extends BaseRepository
{
    /**
     * Inject the workspace model used by the repository.
     */
    public function __construct(Workspace $model)
    {
        parent::__construct($model);
    }

    /**
     * Create a new workspace query builder.
     */
    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Return all workspaces that belong to a user.
     */
    public function forUser(int $userId): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->get();
    }


    /**
     * Paginate workspaces for a user with optional search and sorting.
     *
     * @param int $userId
     * @param string|null $search
     * @param string $sort
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateForUser(int $userId, ?string $search, string $sort = 'desc', int $perPage = 10): LengthAwarePaginator
    {
        return $this->query()
            ->where('user_id', $userId)
            ->when($search, fn (Builder $query) => $query->searchName($search))
            ->orderBy('id', $sort === 'asc' ? 'asc' : 'desc')
            ->paginate($perPage)
            ->appends(['search' => $search, 'sort' => $sort]);
    }

    /**
     * Find a user workspace by primary key or throw when it does not exist.
     *
     * @param int|string $id
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findForUserOrFail(int|string $id, int $userId): Workspace
    {
        return $this->query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    /**
     * Return the highest workspace identifier currently stored.
     */
    public function maxId(): int
    {
        return (int) $this->query()->max('id');
    }

    /**
     * Count all stored workspaces.
     */
    public function count(): int
    {
        return $this->query()->count();
    }

    /**
     * Count workspaces that belong to a user.
     */
    public function countForUser(int $userId): int
    {
        return $this->query()->where('user_id', $userId)->count();
    }

    /**
     * Paginate workspaces for the admin panel with filters.
     *
     * @param int|null $userId
     * @param string|null $search
     * @param string $sort
     * @param int $perPage
     * @return LengthAwarePaginator
     */
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
