<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait for adding dynamic filtering capabilities to Eloquent models.
 *
 * Allows models to define filterable fields and custom filter methods
 * for flexible query building.
 *
 * @property array $filterable List of fields that can be filtered
 */
trait Filterable
{
    /**
     * Apply filters to the query.
     *
     * Checks for custom filter methods (filterFieldName) or applies
     * default filtering for fields listed in $filterable.
     *
     * @param Builder $query The query builder
     * @param array $filters Key-value pairs of field names and values
     * @return Builder The modified query builder
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $method = 'filter' . str_replace('_', '', ucwords($field, '_'));

            if (method_exists($this, $method)) {
                $this->$method($query, $value);
            } elseif ($this->isFilterable($field)) {
                $this->applyDefaultFilter($query, $field, $value);
            }
        }

        return $query;
    }

    /**
     * Check if a field is in the filterable list.
     *
     * @param string $field The field name to check
     * @return bool True if the field can be filtered
     */
    protected function isFilterable(string $field): bool
    {
        return in_array($field, $this->filterable ?? []);
    }

    /**
     * Apply the default filter logic for a field.
     *
     * Uses whereIn for arrays, where for single values.
     *
     * @param Builder $query The query builder
     * @param string $field The field name
     * @param mixed $value The filter value
     * @return void
     */
    protected function applyDefaultFilter(Builder $query, string $field, mixed $value): void
    {
        if (is_array($value)) {
            $query->whereIn($field, $value);
        } else {
            $query->where($field, $value);
        }
    }
}
