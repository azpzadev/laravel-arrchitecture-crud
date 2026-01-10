<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Sortable
{
    public function scopeSort(Builder $query, ?string $field = null, string $direction = 'asc'): Builder
    {
        $field = $field ?? $this->getDefaultSortField();
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        if (!$this->isSortable($field)) {
            $field = $this->getDefaultSortField();
        }

        return $query->orderBy($field, $direction);
    }

    protected function isSortable(string $field): bool
    {
        return in_array($field, $this->sortable ?? ['created_at', 'updated_at']);
    }

    protected function getDefaultSortField(): string
    {
        return $this->defaultSortField ?? 'created_at';
    }
}
