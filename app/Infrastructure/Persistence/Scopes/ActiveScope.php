<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global scope that filters queries to only active records.
 *
 * When applied to a model, automatically adds a where clause
 * to filter out non-active records. Provides macros to bypass
 * or invert this filtering.
 */
class ActiveScope implements Scope
{
    /**
     * Apply the active scope to the query.
     *
     * @param Builder $builder The query builder
     * @param Model $model The model being queried
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where($model->getTable() . '.status', 'active');
    }

    /**
     * Extend the builder with macros for bypassing the scope.
     *
     * Adds 'withInactive' to include inactive records and
     * 'onlyInactive' to show only inactive records.
     *
     * @param Builder $builder The query builder
     * @return void
     */
    public function extend(Builder $builder): void
    {
        $builder->macro('withInactive', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });

        $builder->macro('onlyInactive', function (Builder $builder) {
            return $builder->withoutGlobalScope($this)->where('status', '!=', 'active');
        });
    }
}
