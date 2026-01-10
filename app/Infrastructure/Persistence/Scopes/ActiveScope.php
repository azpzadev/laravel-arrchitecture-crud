<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ActiveScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where($model->getTable() . '.status', 'active');
    }

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
