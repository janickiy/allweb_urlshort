<?php

namespace App\Repositories;

use App\Models\Link;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LinkRepository extends BaseRepository
{
    public function __construct(Link $model)
    {
        parent::__construct($model);
    }

    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function paginateForUser(int $userId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        [$sortColumn, $sortDirection] = $this->sortFromFilter($filters['sort'] ?? null);

        return $this->query()
            ->where('user_id', $userId)
            ->when($filters['domain'] ?? null, fn (Builder $query, mixed $domain) => $query->searchDomain($domain))
            ->when($filters['space'] ?? null, fn (Builder $query, mixed $space) => $query->searchSpace($space))
            ->when($filters['type'] ?? null, function (Builder $query, mixed $type) {
                return (int) $type === 1 ? $query->searchActive() : $query->searchExpired();
            })
            ->when($filters['search'] ?? null, function (Builder $query, mixed $search) use ($filters) {
                return match ($filters['by'] ?? null) {
                    'url' => $query->searchUrl($search),
                    'alias' => $query->searchAlias($search),
                    default => $query->searchTitle($search),
                };
            })
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage)
            ->appends([
                'search' => $filters['search'] ?? null,
                'domain' => $filters['domain'] ?? null,
                'space' => $filters['space'] ?? null,
                'by' => $filters['by'] ?? null,
                'sort' => $filters['sort'] ?? null,
            ]);
    }

    public function findForUser(int|string $id, int $userId): ?Link
    {
        return $this->query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    public function findForUserOrFail(int|string $id, int $userId): Link
    {
        return $this->query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function latestForUser(int $userId, int $limit): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    public function paginateLatestForUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function latest(int $limit): Collection
    {
        return $this->query()
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    public function maxId(): int
    {
        return (int) $this->query()->max('id');
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function paginateForAdmin(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        [$sortColumn, $sortDirection] = $this->sortFromFilter($filters['sort'] ?? null);

        return $this->query()
            ->when($filters['type'] ?? null, function (Builder $query, mixed $type) {
                return (int) $type === 1 ? $query->searchActive() : $query->searchExpired();
            })
            ->when($filters['search'] ?? null, function (Builder $query, mixed $search) use ($filters) {
                return match ($filters['by'] ?? null) {
                    'url' => $query->searchUrl($search),
                    'alias' => $query->searchAlias($search),
                    default => $query->searchTitle($search),
                };
            })
            ->when($filters['user_id'] ?? null, fn (Builder $query, mixed $userId) => $query->userId($userId))
            ->when($filters['space_id'] ?? null, fn (Builder $query, mixed $spaceId) => $query->spaceId($spaceId))
            ->when($filters['domain_id'] ?? null, fn (Builder $query, mixed $domainId) => $query->domainId($domainId))
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage)
            ->appends([
                'search' => $filters['search'] ?? null,
                'by' => $filters['by'] ?? null,
                'sort' => $filters['sort'] ?? null,
                'user_id' => $filters['user_id'] ?? null,
            ]);
    }

    public function countForUser(int $userId): int
    {
        return $this->query()->where('user_id', $userId)->count();
    }

    public function countForSpace(int $userId, int $spaceId): int
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('space_id', $spaceId)
            ->count();
    }

    public function countForDomain(int $userId, int $domainId): int
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('domain_id', $domainId)
            ->count();
    }

    public function findByAliasForDomain(string $alias, ?int $domainId): ?Link
    {
        return $this->query()
            ->where('alias', $alias)
            ->where('domain_id', $domainId)
            ->first();
    }

    public function aliasExists(string $alias, ?int $domainId, int|string|null $exceptId = null): bool
    {
        return $this->query()
            ->where('alias', $alias)
            ->where('domain_id', $domainId)
            ->when($exceptId, fn (Builder $query) => $query->where('id', '!=', $exceptId))
            ->exists();
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    public function bulkInsert(array $rows): bool
    {
        if ($rows === []) {
            return true;
        }

        return $this->query()->insert($rows);
    }

    public function incrementClicks(Link $link): bool
    {
        return $link->forceFill(['clicks' => ((int) $link->clicks) + 1])->save();
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function sortFromFilter(?string $sort): array
    {
        return match ($sort) {
            'min' => ['clicks', 'asc'],
            'max' => ['clicks', 'desc'],
            'asc' => ['id', 'asc'],
            default => ['id', 'desc'],
        };
    }
}
