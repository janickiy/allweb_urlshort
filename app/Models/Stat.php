<?php

namespace App\Models;

use App\Traits\StaticTableName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Stat
 *
 * @mixin Builder
 * @package App
 */
class Stat extends Model
{
    use StaticTableName;

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public $timestamps = false;
}
