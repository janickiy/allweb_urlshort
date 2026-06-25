<?php

namespace App\Models;

use App\Traits\StaticTableName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Cashier\Subscription as CashierSubscription;

/**
 * Class Subscription
 *
 * @mixin Builder
 * @package App
 */
class Subscription extends CashierSubscription
{
    use StaticTableName;

    /**
     * Get the plan matched to this subscription name, including trashed plans.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'name', 'name')->where('name', $this->name)->withTrashed();
    }

    /**
     * Filter subscriptions by a partial Stripe subscription ID match.
     *
     * @param Builder $query
     * @param string $value
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $value): Builder
    {
        return $query->where('stripe_id', 'like', '%' . $value . '%');
    }

    /**
     * Filter subscriptions by Stripe status.
     *
     * @param Builder $query
     * @param string $value
     * @return Builder
     */
    public function scopeStatus(Builder $query, string $value): Builder {
        return $query->where('stripe_status', '=', $value);
    }

    /**
     * Filter subscriptions by plan name.
     *
     * @param Builder $query
     * @param string $value
     * @return Builder
     */
    public function scopePlan(Builder $query, string $value): Builder {
        return $query->where('name', '=', $value);
    }

    /**
     * Filter subscriptions by owner user ID.
     *
     * @param Builder $query
     * @param int|string $value
     * @return Builder
     */
    public function scopeUserId(Builder $query, int|string $value): Builder
    {
        return $query->where('user_id', '=', $value);
    }
}
