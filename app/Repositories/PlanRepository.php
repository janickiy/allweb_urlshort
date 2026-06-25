<?php

namespace App\Repositories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PlanRepository extends BaseRepository
{
    /**
     * Inject the plan model used by the repository.
     */
    public function __construct(Plan $model)
    {
        parent::__construct($model);
    }

    /**
     * Create a new plan query builder.
     */
    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Return plans that are visible to users.
     */
    public function visible(): Collection
    {
        return $this->query()
            ->where('visibility', 1)
            ->get();
    }

    /**
     * Return the free plan when it exists.
     */
    public function free(): ?Plan
    {
        return $this->query()
            ->where('amount_month', 0)
            ->where('amount_year', 0)
            ->first();
    }

    /**
     * Return visible paid plans.
     */
    public function paid(): Collection
    {
        return $this->query()
            ->where('amount_month', '>', 0)
            ->where('amount_year', '>', 0)
            ->get();
    }

    /**
     * Find a paid plan by primary key or throw when it does not exist.
     */
    public function paidByIdOrFail(int|string $id)
    {
        return $this->query()
            ->where('id', $id)
            ->where('amount_month', '>', 0)
            ->where('amount_year', '>', 0)
            ->firstOrFail();
    }

    /**
     * Find a plan by name including trashed records or throw.
     */
    public function withTrashedByNameOrFail(string $name)
    {
        return $this->queryWithTrashed()
            ->where('name', $name)
            ->firstOrFail();
    }

    /**
     * Find a plan by primary key including trashed records or throw.
     */
    public function withTrashedFindOrFail(int|string $id)
    {
        return $this->queryWithTrashed()->findOrFail($id);
    }

    /**
     * Count plans including trashed records.
     */
    public function withTrashedCount(): int
    {
        return $this->queryWithTrashed()->count();
    }

    /**
     * Paginate plans for the admin panel with filters.
     *
     * @param string|null $search
     * @param mixed $visibility
     * @param mixed $status
     * @param string $sort
     * @param int $perPage
     * @return LengthAwarePaginator
     */
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

    /**
     * Find a plan by Stripe plan identifier or throw.
     */
    public function findByStripePlanOrFail(string $stripePlan)
    {
        return $this->query()
            ->where('plan_month', $stripePlan)
            ->orWhere('plan_year', $stripePlan)
            ->firstOrFail();
    }

    /**
     * Restore a soft-deleted plan by primary key.
     */
    public function restore(int|string $id): bool
    {
        $plan = $this->withTrashedFindOrFail($id);

        return (bool) $plan->restore();
    }

    /**
     * Create a plan query builder including trashed records.
     */
    private function queryWithTrashed(): Builder
    {
        return $this->model->newQuery()->withTrashed();
    }
}
