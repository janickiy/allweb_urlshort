<?php

namespace App\Repositories;

use App\Models\Domain;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class DomainRepository extends BaseRepository
{
    /**
     * Inject the domain model used by the repository.
     */
    public function __construct(Domain $model)
    {
        parent::__construct($model);
    }

    /**
     * Create a new domain query builder.
     */
    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Return all domains that belong to a user.
     *
     * @param int $userId
     * @return Collection
     */
    public function forUser(int $userId): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->get();
    }

    /**
     * Paginate domains for a user with optional search and sorting.
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
     * Find a user domain by primary key or throw when it does not exist.
     *
     * @param int|string $id
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findForUserOrFail(int|string $id, int $userId)
    {
        return $this->query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    /**
     * Find a domain by normalized host value.
     */
    public function findByHost(string $host): ?Domain
    {
        $host = $this->normalizeHost($host);

        return $this->query()
            ->whereIn('name', [$host, 'http://'.$host, 'https://'.$host])
            ->first();
    }

    /**
     * Return the highest domain identifier currently stored.
     */
    public function maxId(): int
    {
        return (int) $this->query()->max('id');
    }

    /**
     * Count domains that belong to a user.
     *
     * @param int $userId
     * @return int
     */
    public function countForUser(int $userId): int
    {
        return $this->query()->where('user_id', $userId)->count();
    }

    /**
     * Paginate domains for the admin panel with filters.
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

    /**Normalize a URL or host string before domain lookup.
     *
     *
     * @param string $host
     * @return string
     */
    private function normalizeHost(string $host): string
    {
        $parsed = parse_url($host);

        if (isset($parsed['host'])) {
            return $parsed['host'];
        }

        return (string) preg_replace('/^https?:\/\//', '', $host);
    }
}
