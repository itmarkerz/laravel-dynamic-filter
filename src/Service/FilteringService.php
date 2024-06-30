<?php

namespace Itmarkerz\LaravelDynamicFilter\Service;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;

class FilteringService
{
    public static function applyFilters(string $modelName, array $filters, array $columns = [])
    {
        $defaultNamespace = Config::get('dynamic-filter.default_model_namespace');
        $modelName = class_exists($modelName) ? $modelName : $defaultNamespace . $modelName;

        if (!class_exists($modelName) || !is_subclass_of($modelName, Model::class)) {
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
        $allowedConditions = Config::get('dynamic-filter.allowed_conditions');
        $column = ucfirst(str_replace('_', '', $filterValues['column']));
        $condition = $filterValues['condition'];
        $value = $filterValues['value'];

        if (!in_array($condition, $allowedConditions)) {
            throw new \InvalidArgumentException("Condition '$condition' is not allowed");
        }

        switch ($condition) {
            case 'equals':
                $query->where($column, '=', $value);
                break;
            case 'contains':
                $query->where($column, 'like', "%$value%");
                break;
            case 'greater_than':
                $query->where($column, '>', $value);
                break;
            case 'less_than':
                $query->where($column, '<', $value);
                break;
            default:
                // Handle unknown condition
                break;
        }
    }

    public function getFilterableColumns(string $modelName): array
    {
        $defaultNamespace = Config::get('dynamic-filter.default_model_namespace');
        $modelName = class_exists($modelName) ? $modelName : $defaultNamespace . $modelName;

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
