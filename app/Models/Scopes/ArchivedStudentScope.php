<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Hides archived students from every normal Student query.
 *
 * Adds two helpers (same idea as SoftDeletes):
 *   Student::onlyArchived()  -> archived students only
 *   Student::withArchived()  -> active + archived
 */
class ArchivedStudentScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereNull($model->qualifyColumn('archived_at'));
    }

    public function extend(Builder $builder): void
    {
        $builder->macro('withArchived', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });

        $builder->macro('onlyArchived', function (Builder $builder) {
            return $builder->withoutGlobalScope($this)
                ->whereNotNull($builder->getModel()->qualifyColumn('archived_at'));
        });
    }
}