<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class Link
 *
 * @mixin Builder
 * @package App
 */
class Link extends Model
{
    protected $dates = ['ends_at', 'created_at', 'updated_at'];

    protected $casts = [
        'geo_target' => 'object',
        'platform_target' => 'object'
    ];

    /**
     * Get the space that owns this link.
     */
    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    /**
     * Get the custom domain assigned to this link.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Calculate the total number of click records for this link.
     */
    public function getTotalClicksAttribute(): int
    {
        return $this->hasMany(Stat::class)->where('link_id', $this->id)->count();
    }

    /**
     * Get the click statistics recorded for this link.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stats(): HasMany
    {
        return $this->hasMany(Stat::class)->where('link_id', $this->id);
    }

    /**
     * Get the user that owns this link, including soft-deleted users.
     *
     * @return mixed
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->where('id', $this->user_id)->withTrashed();
    }

    /**
     * Filter links by a partial title match.
     *
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchTitle(Builder $query, mixed $value): Builder
    {
        return $query->where('title', 'like', '%' . $value . '%');
    }

    /**
     * Filter links by a partial destination URL match.
     *
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchUrl(Builder $query, mixed $value): Builder
    {
        return $query->where('url', 'like', '%' . $value . '%');
    }

    /**
     * Filter links by a partial alias match.
     *
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchAlias(Builder $query, mixed $value): Builder
    {
        return $query->where('alias', 'like', '%' . $value . '%');
    }

    /**
     * Filter links by space ID.
     *
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchSpace(Builder $query, mixed $value): Builder
    {
        return $query->where('space_id', '=', $value);
    }

    /**
     * Filter links by domain ID.
     *
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchDomain(Builder $query, mixed $value): Builder
    {
        return $query->where('domain_id', '=', $value);
    }

    /**
     * Filter links whose expiration date is in the past.
     *
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchExpired(Builder $query): Builder
    {
        return $query->whereNotNull('ends_at')->where('ends_at', '<', Carbon::now());
    }

    /**
     * Filter links that have not expired yet.
     *
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchActive(Builder $query): Builder
    {
        return $query->whereNull('ends_at')->orWhere('ends_at', '>', Carbon::now());
    }

    /**
     * Filter links by owner user ID.
     *
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeUserId(Builder $query, mixed $value): Builder
    {
        return $query->where('user_id', '=', $value);
    }

    /**
     * Filter links by assigned space ID.
     *
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSpaceId(Builder $query, mixed $value): Builder
    {
        return $query->where('space_id', '=', $value);
    }

    /**
     * Filter links by assigned domain ID.
     *
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeDomainId(Builder $query, mixed $value): Builder
    {
        return $query->where('domain_id', '=', $value);
    }
}
