<?php

namespace App\Repositories;

use App\Models\Stat;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

class StatRepository extends BaseRepository
{
    private const GROUPABLE_COLUMNS = [
        'country',
        'browser',
        'platform',
        'device',
        'referrer',
        'language',
    ];

    /**
     * Inject the stat model used by the repository.
     */
    public function __construct(Stat $model)
    {
        parent::__construct($model);
    }

    /**
     * Return the latest statistics for links owned by a user.
     */
    public function latestForUser(int $userId, int $limit): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Return the highest stat identifier currently stored.
     */
    public function maxId(): int
    {
        return (int) $this->query()->max('id');
    }

    /**
     * Paginate statistics for a link.
     */
    public function paginateForLink(int $linkId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->query()
            ->where('link_id', $linkId)
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Count link statistics since a given date.
     */
    public function countForLinkSince(int $linkId, Carbon $since): int
    {
        return $this->query()
            ->where('link_id', $linkId)
            ->whereDate('created_at', '>', $since)
            ->count();
    }

    /**
     * Count link statistics between two dates.
     */
    public function countForLinkBetween(int $linkId, Carbon $start, Carbon $end): int
    {
        return $this->query()
            ->where('link_id', $linkId)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    /**
     * Return grouped statistics for a link by a selected column.
     *
     * @param int $linkId
     * @param string $column
     * @param array|null $values
     * @param bool $paginate
     * @param int $perPage
     * @return LengthAwarePaginator|Collection
     */
    public function groupForLink(int $linkId, string $column, ?array $values = null, bool $paginate = true, int $perPage = 10): LengthAwarePaginator|Collection
    {
        $query = $this->groupQuery($linkId, $column)
            ->when($values !== null, fn (Builder $builder) => $builder->whereIn($column, $values));

        return $paginate ? $query->paginate($perPage) : $query->get();
    }


    /**
     * Build the aggregate query used for grouped link statistics.
     *
     * @param int $linkId
     * @param string $column
     * @return Builder
     */
    private function groupQuery(int $linkId, string $column): Builder
    {
        if (!in_array($column, self::GROUPABLE_COLUMNS, true)) {
            throw new InvalidArgumentException("Unsupported stat group column [{$column}].");
        }

        return $this->query()
            ->select($column)
            ->selectRaw('COUNT(*) as `count`')
            ->where('link_id', $linkId)
            ->groupBy($column)
            ->orderByDesc('count');
    }
}
