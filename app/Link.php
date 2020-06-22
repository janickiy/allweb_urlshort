<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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

    public function space()
    {
        return $this->belongsTo('App\Space');
    }

    public function domain()
    {
        return $this->belongsTo('App\Domain');
    }

    public function getTotalClicksAttribute()
    {
        return $this->hasMany('App\Stat')->where('link_id', $this->id)->count();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stats()
    {
        return $this->hasMany('App\Stat')->where('link_id', $this->id);
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\User')->where('id', $this->user_id)->withTrashed();
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchTitle(Builder $query, $value)
    {
        return $query->where('title', 'like', '%' . $value . '%');
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchUrl(Builder $query, $value)
    {
        return $query->where('url', 'like', '%' . $value . '%');
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchAlias(Builder $query, $value)
    {
        return $query->where('alias', 'like', '%' . $value . '%');
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchSpace(Builder $query, $value)
    {
        return $query->where('space_id', '=', $value);
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchDomain(Builder $query, $value)
    {
        return $query->where('domain_id', '=', $value);
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchExpired(Builder $query)
    {
        return $query->whereNotNull('ends_at')->where('ends_at', '<', Carbon::now());
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearchActive(Builder $query)
    {
        return $query->whereNull('ends_at')->orWhere('ends_at', '>', Carbon::now());
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeUserId(Builder $query, $value)
    {
        return $query->where('user_id', '=', $value);
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSpaceId(Builder $query, $value)
    {
        return $query->where('space_id', '=', $value);
    }

    /**
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeDomainId(Builder $query, $value)
    {
        return $query->where('domain_id', '=', $value);
    }
}
