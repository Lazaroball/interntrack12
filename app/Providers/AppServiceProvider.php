<?php

namespace App\Providers;

use App\Models\DailyLog;
use App\Models\Deployment;
use App\Models\Evaluation;
use App\Models\FieldStudyRequest;
use App\Models\Requirement;
use App\Models\Scopes\ArchivedStudentScope;
use App\Models\Scopes\HideArchivedStudentRows;
use App\Models\Student;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Archived students disappear from every normal Student query.
        Student::addGlobalScope(new ArchivedStudentScope);

        // Rows that belong to an archived student are hidden too
        // (dashboard counts, school slots, supervisor/coordinator lists).
        $studentOwnedModels = [
            Deployment::class,
            Evaluation::class,
            Requirement::class,
            DailyLog::class,
            FieldStudyRequest::class,
            \App\Models\ObservationSchedule::class,
            \App\Models\LessonPlan::class,
        ];

        foreach ($studentOwnedModels as $model) {
            if (class_exists($model)) {
                $model::addGlobalScope(new HideArchivedStudentRows);
            }
        }
    }
}