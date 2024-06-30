<?php

namespace Itmarkerz\LaravelDynamicFilter\Service;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FilteringService
{
    public static function applyFilters(string $modelName, array $filters, array $columns = [])
    {
        if (!class_exists($modelName) || !is_subclass_of($modelName, 'Illuminate\Database\Eloquent\Model')) {
            throw new \InvalidArgumentException('Invalid model name provided');
        }

        $query = $modelName::query();

        if (empty($columns)) {
            $columns = $modelName::getFilterableColumns();
        }

        foreach ($filters as $filter) {
            $logic = $filter['logic'] ?? 'and';
            $filterValues = $filter['filter_values'] ?? [];

            if ($logic === 'and') {
                $query->where(function ($q) use ($filterValues) {
                    self::applyCondition($q, $filterValues);
                });
            } else {
                $query->orWhere(function ($q) use ($filterValues) {
                    self::applyCondition($q, $filterValues);
                });
            }
        }

        return $query->select($columns);
    }

    private static function applyCondition($query, $filterValues)
    {
        $column = ucfirst(str_replace('_', '', $filterValues['column']));
        $condition = $filterValues['condition'];
        $value = $filterValues['value'];

        switch ($condition) {
            case 'equals':
                $query->where($column, '=', $value);
                break;
            case 'not_equals':
                $query->where($column, '!=', $value);
                break;
            case 'contains':
                $query->where($column, 'like', "%$value%");
                break;
            case 'not_contains':
                $query->where($column, 'not like', "%$value%");
                break;
            case 'starts_with':
                $query->where($column, 'like', "$value%");
                break;
            case 'ends_with':
                $query->where($column, 'like', "%$value");
                break;
            case 'greater_than':
                $query->where($column, '>', $value);
                break;
            case 'less_than':
                $query->where($column, '<', $value);
                break;
            case 'greater_than_or_equal':
                $query->where($column, '>=', $value);
                break;
            case 'less_than_or_equal':
                $query->where($column, '<=', $value);
                break;
            case 'in':
                $query->whereIn($column, is_array($value) ? $value : [$value]);
                break;
            case 'not_in':
                $query->whereNotIn($column, is_array($value) ? $value : [$value]);
                break;
            case 'between':
                if (is_array($value) && count($value) == 2) {
                    $query->whereBetween($column, $value);
                }
                break;
            case 'not_between':
                if (is_array($value) && count($value) == 2) {
                    $query->whereNotBetween($column, $value);
                }
                break;
            case 'is_null':
                $query->whereNull($column);
                break;
            case 'is_not_null':
                $query->whereNotNull($column);
                break;
            default:
               
                break;
        }
    
    }
    public function getFilterableColumns(string $modelName): array
    {
        if (!class_exists($modelName) || !is_subclass_of($modelName, Model::class)) {
            throw new \InvalidArgumentException('Invalid model name provided');
        }

        if (!method_exists($modelName, 'getFilterableColumns')) {
            throw new \BadMethodCallException('Model does not support filtering');
        }

        $columns = $modelName::getFilterableColumns();

        return array_map(function($column) {
            return Str::camel($column);
        }, $columns);
    }
}