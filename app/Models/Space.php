<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Space
 *
 * @mixin Builder
 * @package App
 */
class Space extends Model
{
    /**
     * Filter spaces by a partial name match.
     *
     * @param Builder $query
     * @param string $value
     * @return Builder
     */
    public function scopeSearchName(Builder $query, string $value): Builder
    {
        return $query->where('name', 'like', '%' . $value . '%');
    }

    /**
     * Calculate the total number of links assigned to this space.
     */
    public function getTotalLinksAttribute(): int
    {
        return $this->hasMany(Link::class)->where('space_id', $this->id)->count();
    }

    /**
     * Get the links assigned to this space.
     */
    public function links(): HasMany
    {
        return $this->hasMany(Link::class)->where('space_id', $this->id);
    }

    /**
     * Get the user that owns this space, including soft-deleted users.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->where('id', $this->user_id)->withTrashed();
    }

    /**
     * Filter spaces by owner user ID.
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
