<?php

namespace App\Repositories;

use App\Models\Subscription;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SubscriptionRepository extends BaseRepository
{
    public function __construct(Subscription $model)
    {
        parent::__construct($model);
    }

    public function findForUserOrFail(int|string $id, int $userId): Subscription
    {
        return $this->query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function recent(int $limit): Collection
    {
        return $this->query()
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    public function count(): int
    {
        return $this->query()->count();
    }

    public function countForUser(int $userId): int
    {
        return $this->query()
            ->where('user_id', $userId)
            ->count();
    }

    public function emulatedOrFail(int|string $id): Subscription
    {
        return $this->query()
            ->where('id', $id)
            ->where('stripe_status', 'emulated')
            ->firstOrFail();
    }

    public function renamePlan(string $oldName, string $newName): int
    {
        return $this->query()
            ->where('name', $oldName)
            ->update(['name' => $newName]);
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function paginateForAdmin(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->query()
            ->with(['user' => function ($query) {
                $query->withTrashed();
            }])
            ->when($filters['status'] ?? null, fn (Builder $query, mixed $status) => $query->status($status))
            ->when($filters['plan'] ?? null, fn (Builder $query, mixed $plan) => $query->plan($plan))
            ->when($filters['user_id'] ?? null, fn (Builder $query, mixed $userId) => $query->userId($userId))
            ->orderBy('id', ($filters['sort'] ?? null) === 'asc' ? 'asc' : 'desc')
            ->paginate($perPage)
            ->appends([
                'search' => $filters['search'] ?? null,
                'status' => $filters['status'] ?? null,
                'plan' => $filters['plan'] ?? null,
                'user_id' => $filters['user_id'] ?? null,
            ]);
    }
}
