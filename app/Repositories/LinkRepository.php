<?php

namespace App\Repositories;

use App\DTO\DataTransferObject;
use App\DTO\LinkData;
use App\Models\Link;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class LinkRepository extends BaseRepository
{
    /**
     * Inject the link model used by the repository.
     */
    public function __construct(Link $model)
    {
        parent::__construct($model);
    }

    /**
     * Create a new link query builder with its default relationships.
     */
    public function query(): Builder
    {
        return $this->model->newQuery();
    }


    /**
     * Paginate links that belong to a user with filters.
     *
     * @param int $userId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateForUser(int $userId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        [$sortColumn, $sortDirection] = $this->sortFromFilter($filters['sort'] ?? null);

        return $this->query()
            ->where('user_id', $userId)
            ->when($filters['domain'] ?? null, fn (Builder $query, mixed $domain) => $query->searchDomain($domain))
            ->when($filters['workspace'] ?? null, fn (Builder $query, mixed $workspace) => $query->searchWorkspace($workspace))
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
                'workspace' => $filters['workspace'] ?? null,
                'by' => $filters['by'] ?? null,
                'sort' => $filters['sort'] ?? null,
            ]);
    }

    /**
     * Find a user link by primary key or return null.
     *
     * @param int|string $id
     * @param int $userId
     * @return Link|null
     */
    public function findForUser(int|string $id, int $userId): ?Link
    {
        return $this->query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Find a user link by primary key or throw when it does not exist.
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
     * Return the latest links for a user.
     *
     * @param int $userId
     * @param int $limit
     * @return Collection
     */
    public function latestForUser(int $userId, int $limit): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Paginate the latest links for a user.
     *
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateLatestForUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Return the latest links across all users.
     */
    public function latest(int $limit): Collection
    {
        return $this->query()
            ->with(['workspace', 'domain'])
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Return the links with the highest click counters.
     */
    public function topByClicks(int $limit): Collection
    {
        return $this->query()
            ->with(['workspace', 'domain'])
            ->orderBy('clicks', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Count all stored links.
     */
    public function count(): int
    {
        return $this->query()->count();
    }

    /**
     * Count links that are currently published.
     */
    public function publishedCount(): int
    {
        return $this->query()->where('disabled', 0)->count();
    }

    /**
     * Count links that are blocked or disabled.
     */
    public function blockedCount(): int
    {
        return $this->query()->where('disabled', 1)->count();
    }

    /**
     * Return the highest link identifier currently stored.
     */
    public function maxId(): int
    {
        return (int) $this->query()->max('id');
    }

    /**
     * Paginate links for the admin panel with filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
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
            ->when($filters['workspace_id'] ?? null, fn (Builder $query, mixed $workspaceId) => $query->workspaceId($workspaceId))
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

    /**
     * Count links that belong to a user.
     */
    public function countForUser(int $userId): int
    {
        return $this->query()->where('user_id', $userId)->count();
    }

    /**
     * Count links that belong to a user workspace
     *
     * @param int $userId
     * @param int $workspaceId
     * @return int
     */
    public function countForWorkspace(int $userId, int $workspaceId): int
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('workspace_id', $workspaceId)
            ->count();
    }

    /**
     * Count links that belong to a user domain.
     *
     * @param int $userId
     * @param int $domainId
     * @return int
     */
    public function countForDomain(int $userId, int $domainId): int
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('domain_id', $domainId)
            ->count();
    }

    /**
     * Find a link by alias and domain scope.
     *
     * @param string $alias
     * @param int|null $domainId
     * @return Link|null
     */
    public function findByAliasForDomain(string $alias, ?int $domainId): ?Link
    {
        return $this->query()
            ->where('alias', $alias)
            ->where('domain_id', $domainId)
            ->first();
    }

    /**
     * Return the assigned domain name for a link when it exists.
     */
    public function domainName(Link $link): ?string
    {
        if ($link->domain_id === null) {
            return null;
        }

        if ($link->relationLoaded('domain')) {
            return $link->domain?->name;
        }

        $domainName = $link->domain()->value('name');

        return $domainName === null ? null : (string) $domainName;
    }

    /**
     * Check whether an alias already exists in a domain scope.
     *
     * @param string $alias
     * @param int|null $domainId
     * @param int|string|null $exceptId
     * @return bool
     */
    public function aliasExists(string $alias, ?int $domainId, int|string|null $exceptId = null): bool
    {
        return $this->query()
            ->where('alias', $alias)
            ->where('domain_id', $domainId)
            ->when($exceptId, fn (Builder $query) => $query->where('id', '!=', $exceptId))
            ->exists();
    }

    /**
     * Insert multiple link rows in one database operation.
     *
     * @param array<int, array<string, mixed>> $rows
     */
    protected function bulkInsert(array $rows): bool
    {
        if ($rows === []) {
            return true;
        }

        return $this->query()->insert($rows);
    }

    /**
     * Insert multiple links from data transfer objects in one database operation.
     *
     * @param array<int, DataTransferObject> $dtos
     */
    public function bulkInsertFromDtos(array $dtos): bool
    {
        $now = Carbon::now();

        return $this->bulkInsert(array_map(
            fn (DataTransferObject $dto): array => array_merge($dto->toArray(), [
                'created_at' => $now,
                'updated_at' => $now,
            ]),
            $dtos
        ));
    }

    /**
     * Increment the click counter for a link.
     */
    public function incrementClicks(Link $link): bool
    {
        return $this->updateFromDto($link->id, LinkData::fromArray([
            'clicks' => ((int) $link->clicks) + 1,
        ]));
    }

    /**
     * Resolve a link sort filter into column and direction values.
     *
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
