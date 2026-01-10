<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait for adding search capabilities to Eloquent models.
 *
 * Allows models to define searchable fields including
 * relationship fields using dot notation.
 *
 * @property array $searchable List of fields to search (supports relation.field notation)
 */
trait Searchable
{
    /**
     * Apply search term to searchable fields.
     *
     * Searches across all fields defined in $searchable using
     * case-insensitive ILIKE matching. Supports searching
     * through relationships using dot notation.
     *
     * @param Builder $query The query builder
     * @param string|null $term The search term
     * @return Builder The modified query builder
     */
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
