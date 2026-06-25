<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Plan
 *
 * @mixin Builder
 * @package App
 */
class Plan extends Model
{
    use SoftDeletes;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Filter plans by a partial name match.
     *
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearch(Builder $query, mixed $value): Builder
    {
        return $query->where('name', 'like', '%' . $value . '%');
    }

    /**
     * Filter plans by visibility value.
     *
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeVisibility(Builder $query, mixed $value): Builder
    {
        return $query->where('visibility', '=', $value);
    }

    /**
     * Sanitize the plan name before storing it.
     *
     * @param $value
     */
    public function setNameAttribute(mixed $value): void
    {
        $this->attributes['name'] = strip_tags($value);
    }
}
