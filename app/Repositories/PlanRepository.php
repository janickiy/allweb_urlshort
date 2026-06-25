<?php

namespace App\Repositories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PlanRepository extends BaseRepository
{
    public function __construct(Plan $model)
    {
        parent::__construct($model);
    }

    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function visible(): Collection
    {
        return $this->query()
            ->where('visibility', 1)
            ->get();
    }

    public function free(): ?Plan
    {
        return $this->query()
            ->where('amount_month', 0)
            ->where('amount_year', 0)
            ->first();
    }

    public function paid(): Collection
    {
        return $this->query()
            ->where('amount_month', '>', 0)
            ->where('amount_year', '>', 0)
            ->get();
    }

    public function paidByIdOrFail(int|string $id): Plan
    {
        return $this->query()
            ->where('id', $id)
            ->where('amount_month', '>', 0)
            ->where('amount_year', '>', 0)
            ->firstOrFail();
    }

    public function withTrashedByNameOrFail(string $name): Plan
    {
        return $this->queryWithTrashed()
            ->where('name', $name)
            ->firstOrFail();
    }

    public function withTrashedFindOrFail(int|string $id): Plan
    {
        return $this->queryWithTrashed()->findOrFail($id);
    }

    public function withTrashedCount(): int
    {
        return $this->queryWithTrashed()->count();
    }

    public function paginateForAdmin(?string $search, mixed $visibility, mixed $status, string $sort = 'desc', int $perPage = 10): LengthAwarePaginator
    {
        $stripe = config('settings.stripe');

        return $this->queryWithTrashed()
            ->when(!$stripe, fn (Builder $query) => $query->where([['amount_month', '=', 0], ['amount_year', '=', 0]]))
            ->when($search, fn (Builder $query) => $query->search($search))
            ->when(isset($visibility) && is_numeric($visibility), fn (Builder $query) => $query->visibility((int) $visibility))
            ->when(isset($status) && is_numeric($status), function (Builder $query) use ($status) {
                return $status ? $query->whereNotNull('deleted_at') : $query->whereNull('deleted_at');
            })
            ->orderBy('id', $sort === 'asc' ? 'asc' : 'desc')
            ->paginate($perPage)
            ->appends(['search' => $search, 'visibility' => $visibility, 'status' => $status, 'sort' => $sort]);
    }

    public function findByStripePlanOrFail(string $stripePlan): Plan
    {
        return $this->query()
            ->where('plan_month', $stripePlan)
            ->orWhere('plan_year', $stripePlan)
            ->firstOrFail();
    }

    public function restore(int|string $id): bool
    {
        $plan = $this->withTrashedFindOrFail($id);

        return (bool) $plan->restore();
    }

    private function queryWithTrashed(): Builder
    {
        return $this->model->newQuery()->withTrashed();
    }
}
