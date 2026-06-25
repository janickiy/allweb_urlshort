<?php

namespace App\Models;

use App\Traits\StaticTableName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Language
 *
 * @mixin Builder
 * @package App
 */
class Language extends Model
{
    use StaticTableName;

    /**
     * @var string
     */
    protected $table = 'languages';

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $primaryKey = 'code';

    /**
     * @var bool
     */
    public $incrementing = false;


    /**
     * Filter languages by a partial name match.
     *
     * @param Builder $query
     * @param string $value
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $value): Builder
    {
        return $query->where('name', 'like', '%'.$value.'%');
    }
}
