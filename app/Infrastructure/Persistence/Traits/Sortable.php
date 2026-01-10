<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait for adding sorting capabilities to Eloquent models.
 *
 * Allows models to define sortable fields and a default sort field
 * for consistent query ordering.
 *
 * @property array $sortable List of fields that can be sorted
 * @property string $defaultSortField The default field to sort by
 */
trait Sortable
{
    /**
     * Apply sorting to the query.
     *
     * Falls back to default sort field if the requested field
     * is not in the sortable list.
     *
     * @param Builder $query The query builder
     * @param string|null $field The field to sort by
     * @param string $direction The sort direction ('asc' or 'desc')
     * @return Builder The modified query builder
     */
    public function scopeSort(Builder $query, ?string $field = null, string $direction = 'asc'): Builder
    {
        $field = $field ?? $this->getDefaultSortField();
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        if (!$this->isSortable($field)) {
            $field = $this->getDefaultSortField();
        }

        return $query->orderBy($field, $direction);
    }

    /**
     * Check if a field is in the sortable list.
     *
     * @param string $field The field name to check
     * @return bool True if the field can be sorted
     */
    protected function isSortable(string $field): bool
    {
        return in_array($field, $this->sortable ?? ['created_at', 'updated_at']);
    }

    /**
     * Get the default sort field.
     *
     * @return string The default field to sort by
     */
    protected function getDefaultSortField(): string
    {
        return $this->defaultSortField ?? 'created_at';
    }
}
