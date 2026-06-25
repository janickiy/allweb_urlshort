<?php

namespace App\Traits;

trait StaticTableName
{
    /**
     * Returns the Eloquent model table name.
     */
    public static function getTableName(): string
    {
        return with(new static)->getTable();
    }
}
