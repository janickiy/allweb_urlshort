<?php

namespace App\Repositories;

use App\Models\Stat;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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

    public function __construct(Stat $model)
    {
        parent::__construct($model);
    }

    public function latestForUser(int $userId, int $limit): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function paginateForLink(int $linkId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->query()
            ->where('link_id', $linkId)
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function countForLinkSince(int $linkId, Carbon $since): int
    {
        return $this->query()
            ->where('link_id', $linkId)
            ->whereDate('created_at', '>', $since)
            ->count();
    }

    public function countForLinkBetween(int $linkId, Carbon $start, Carbon $end): int
    {
        return $this->query()
            ->where('link_id', $linkId)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    public function groupForLink(int $linkId, string $column, ?array $values = null, bool $paginate = true, int $perPage = 10): LengthAwarePaginator|Collection
    {
        $query = $this->groupQuery($linkId, $column)
            ->when($values !== null, fn (Builder $builder) => $builder->whereIn($column, $values));

        return $paginate ? $query->paginate($perPage) : $query->get();
    }

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
