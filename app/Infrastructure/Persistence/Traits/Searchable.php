<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        $searchableFields = $this->searchable ?? [];

        if (empty($searchableFields)) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term, $searchableFields) {
            foreach ($searchableFields as $index => $field) {
                $method = $index === 0 ? 'where' : 'orWhere';

                if (str_contains($field, '.')) {
                    // Handle relationship search
                    [$relation, $relationField] = explode('.', $field, 2);
                    $query->$method(function (Builder $q) use ($relation, $relationField, $term) {
                        $q->whereHas($relation, function (Builder $subQuery) use ($relationField, $term) {
                            $subQuery->where($relationField, 'ilike', "%{$term}%");
                        });
                    });
                } else {
                    $query->$method($field, 'ilike', "%{$term}%");
                }
            }
        });
    }
}
