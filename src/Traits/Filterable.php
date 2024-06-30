<?php

namespace Itmarkerz\DynamicFilter\Traits;

trait Filterable
{
    public static function getFilterableColumns()
    {
        $filterable = (new static)->filterable;
        return array_map(function($column) {
            return ucwords(str_replace('_', ' ', $column));
        }, $filterable);
    }
}
