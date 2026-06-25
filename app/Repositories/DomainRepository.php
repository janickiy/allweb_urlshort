<?php

namespace App\Repositories;

use App\Models\Domain;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DomainRepository extends BaseRepository
{
    public function __construct(Domain $model)
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

    public function findForUserOrFail(int|string $id, int $userId): Domain
    {
        return $this->query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function findByHost(string $host): ?Domain
    {
        $host = $this->normalizeHost($host);

        return $this->query()
            ->whereIn('name', [$host, 'http://'.$host, 'https://'.$host])
            ->first();
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

    private function normalizeHost(string $host): string
    {
        $parsed = parse_url($host);

        if (isset($parsed['host'])) {
            return $parsed['host'];
        }

        return (string) preg_replace('/^https?:\/\//', '', $host);
    }
}
