<?php

namespace App\Models;

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
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public $timestamps = false;
}
