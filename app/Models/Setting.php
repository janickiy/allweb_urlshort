<?php

namespace App\Models;

use App\Traits\StaticTableName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Setting
 *
 * @mixin Builder
 * @package App
 */
class Setting extends Model
{
    use StaticTableName;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    public $timestamps = false;
}
