<?php

namespace App\Repositories;

use App\DTO\DataTransferObject;
use App\Models\Subscription;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionRepository extends BaseRepository
{
    /**
     * Inject the subscription model used by the repository.
     */
    public function __construct(Subscription $model)
    {
        parent::__construct($model);
    }

    /**
     * Find a user subscription by primary key or throw when it does not exist
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
     * Return subscriptions that belong to a user.
     */
    public function forUser(int $userId): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->get();
    }

    /**
     * Find a user subscription by plan name or return null
     *
     * @param int $userId
     * @param string $name
     * @return Subscription|null
     */
    public function findForUserByName(int $userId, string $name): ?Subscription
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('name', $name)
            ->first();
    }

    /**
     * Return the most recent subscriptions.
     */
    public function recent(int $limit): Collection
    {
        return $this->query()
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Count all subscriptions.
     */
    public function count(): int
    {
        return $this->query()->count();
    }

    /**
     * Count subscriptions that belong to a user.
     */
    public function countForUser(int $userId): int
    {
        return $this->query()
            ->where('user_id', $userId)
            ->count();
    }

    /**
     * Find an emulated subscription or throw when it does not exist.
     *
     * @param int|string $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function emulatedOrFail(int|string $id)
    {
        return $this->query()
            ->where('id', $id)
            ->where('stripe_status', 'emulated')
            ->firstOrFail();
    }

    /**
     * Rename the plan name on matching subscriptions.
     *
     * @param string $oldName
     * @param DataTransferObject $dto
     * @return int
     */
    public function renamePlan(string $oldName, DataTransferObject $dto): int
    {
        return $this->query()
            ->where('name', $oldName)
            ->update($dto->toArray());
    }

    /**
     * Paginate subscriptions for the admin panel with filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
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
