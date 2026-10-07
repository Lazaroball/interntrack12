<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * For models that have a student_id column (Deployment, Requirement,
 * DailyLog, Evaluation, ...). Hides rows that belong to an archived student
 * so they disappear from dashboards, slot counts and supervisor/coordinator
 * lists. Nothing is deleted; restoring the student brings the rows back.
 */
class HideArchivedStudentRows implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereNotIn($model->qualifyColumn('student_id'), function ($query) {
            $query->select('id')->from('students')->whereNotNull('archived_at');
        });
    }
}