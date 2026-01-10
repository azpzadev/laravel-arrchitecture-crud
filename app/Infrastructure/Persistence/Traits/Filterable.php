<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
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

    protected function isFilterable(string $field): bool
    {
        return in_array($field, $this->filterable ?? []);
    }

    protected function applyDefaultFilter(Builder $query, string $field, mixed $value): void
    {
        if (is_array($value)) {
            $query->whereIn($field, $value);
        } else {
            $query->where($field, $value);
        }
    }
}
