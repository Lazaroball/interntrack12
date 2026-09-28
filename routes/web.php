<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AdminDashboardController;
/*
|--------------------------------------------------------------------------
| Coordinator
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\CoordinatorDashboardController;
use App\Http\Controllers\Coordinator\StudentManagementController;
use App\Http\Controllers\Coordinator\PartnerSchoolController;
use App\Http\Controllers\Coordinator\DeploymentController as CoordinatorDeploymentController;
use App\Http\Controllers\Coordinator\StudentImportController;
use App\Http\Controllers\StudentDeploymentController;
use App\Http\Controllers\Coordinator\RequirementDefinitionController;
use App\Http\Controllers\Coordinator\RequirementReviewController;
use App\Http\Controllers\Coordinator\CoordinatorFieldStudyRequestController;
/*
|--------------------------------------------------------------------------
| Supervisor
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\SupervisorDashboardController;
use App\Http\Controllers\Supervisor\SupervisorStudentController;
use App\Http\Controllers\Supervisor\ObservationScheduleController;
use App\Http\Controllers\Supervisor\SupervisorEvaluationController;
use App\Http\Controllers\Supervisor\OtherEvaluatorResultController;
use App\Http\Controllers\Supervisor\FieldStudyRequestController;
/*
|--------------------------------------------------------------------------
| Student
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentProfileController;
use App\Http\Controllers\Student\FieldStudyController;
use App\Http\Controllers\Student\RequirementController;
use App\Http\Controllers\Student\StudentFieldStudyRequestController;
use App\Http\Controllers\Student\DailyLogController;
/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Register Success
|--------------------------------------------------------------------------
*/

Route::get('/register-success', function () {
    return view('auth.register-success');
})->name('register.success');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::prefix('admin/users')
        ->name('admin.users.')
        ->group(function () {

            Route::get('/', [AdminUserController::class, 'index'])->name('index');

            Route::get('/create', [AdminUserController::class, 'create'])->name('create');

            Route::post('/', [AdminUserController::class, 'store'])->name('store');

            Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit');

            Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
            

            Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
            

            Route::patch('/{user}/activate', [AdminUserController::class, 'activate'])->name('activate');

            Route::patch('/{user}/deactivate', [AdminUserController::class, 'deactivate'])->name('deactivate');

            Route::patch('/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('resetPassword');
        });

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');
});

/*
|--------------------------------------------------------------------------
| Coordinator Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:coordinator'])
    ->prefix('coordinator')
    ->name('coordinator.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [CoordinatorDashboardController::class, 'index'])
            ->name('dashboard');

        // Student Management
        Route::get('/students', [StudentManagementController::class, 'index'])
            ->name('students.index');

        Route::get('/students/{student}', [StudentManagementController::class, 'show'])
            ->name('students.show');

        // Partner School Management

Route::get('/partner-schools', [PartnerSchoolController::class, 'index'])
    ->name('partner-schools.index');

Route::get('/partner-schools/create', [PartnerSchoolController::class, 'create'])
    ->name('partner-schools.create');

Route::post('/partner-schools', [PartnerSchoolController::class, 'store'])
    ->name('partner-schools.store');

Route::get('/partner-schools/{partnerSchool}', [PartnerSchoolController::class, 'show'])
    ->name('partner-schools.show');

Route::get('/partner-schools/{partnerSchool}/edit', [PartnerSchoolController::class, 'edit'])
    ->name('partner-schools.edit');

Route::put('/partner-schools/{partnerSchool}', [PartnerSchoolController::class, 'update'])
    ->name('partner-schools.update');

Route::delete('/partner-schools/{partnerSchool}', [PartnerSchoolController::class, 'destroy'])
    ->name('partner-schools.destroy');

Route::patch('/partner-schools/{id}/restore', [PartnerSchoolController::class, 'restore'])
    ->name('partner-schools.restore');

Route::patch('/partner-schools/{partnerSchool}/activate', [PartnerSchoolController::class, 'activate'])
    ->name('partner-schools.activate');

Route::patch('/partner-schools/{partnerSchool}/deactivate', [PartnerSchoolController::class, 'deactivate'])
    ->name('partner-schools.deactivate');

Route::get('/student-import/template', [StudentImportController::class, 'downloadTemplate'])
    ->name('students.import.template');

       // ==========================
// Deployment Management
// ==========================

// 1. Static & Creation Routes
Route::get('/deployments', [CoordinatorDeploymentController::class, 'index'])
    ->name('deployments.index');

Route::get('/deployments/create', [CoordinatorDeploymentController::class, 'create'])
    ->name('deployments.create');

Route::post('/deployments', [CoordinatorDeploymentController::class, 'store'])
    ->name('deployments.store');

// 2. Bulk Actions (MUST stay BEFORE {deployment} wildcard routes)
Route::post('/deployments/approve-selected', [CoordinatorDeploymentController::class, 'approveSelected'])
    ->name('deployments.approve-selected');

Route::post('/deployments/approve-all', [CoordinatorDeploymentController::class, 'approveAll'])
    ->name('deployments.approve-all');

Route::patch('/deployments/bulk-update-supervisor', [CoordinatorDeploymentController::class, 'bulkUpdateSupervisor'])
    ->name('deployments.bulk-update-supervisor');

// 3. Individual Parameterized Routes ({deployment} wildcards at the BOTTOM)
Route::get('/deployments/{deployment}', [CoordinatorDeploymentController::class, 'show'])
    ->name('deployments.show');

Route::get('/deployments/{deployment}/edit', [CoordinatorDeploymentController::class, 'edit'])
    ->name('deployments.edit');

Route::put('/deployments/{deployment}', [CoordinatorDeploymentController::class, 'update'])
    ->name('deployments.update');

Route::delete('/deployments/{deployment}', [CoordinatorDeploymentController::class, 'destroy'])
    ->name('deployments.destroy');

// 4. Individual Actions
Route::patch('/deployments/{deployment}/supervisor', [CoordinatorDeploymentController::class, 'updateSupervisor'])
    ->name('deployments.update-supervisor');

Route::patch('/deployments/{deployment}/complete', [CoordinatorDeploymentController::class, 'complete'])
    ->name('deployments.complete');

Route::patch('/deployments/{deployment}/cancel', [CoordinatorDeploymentController::class, 'cancel'])
    ->name('deployments.cancel');

        // ==========================
        // Student Import
        // ==========================

        Route::get('/student-import', [StudentImportController::class, 'index'])
            ->name('students.import');

        Route::post('/student-import/upload', [StudentImportController::class, 'upload'])
            ->name('students.import.upload');

        Route::post('/student-import/process', [StudentImportController::class, 'process'])
            ->name('students.import.process');
            // Requirement Definitions
Route::prefix('requirements/definitions')
    ->name('requirements.definitions.')
    ->group(function () {
        Route::get('/', [RequirementDefinitionController::class, 'index'])->name('index');
        Route::get('/create', [RequirementDefinitionController::class, 'create'])->name('create');
        Route::post('/', [RequirementDefinitionController::class, 'store'])->name('store');
        Route::get('/{definition}/edit', [RequirementDefinitionController::class, 'edit'])->name('edit');
        Route::put('/{definition}', [RequirementDefinitionController::class, 'update'])->name('update');
        Route::patch('/{definition}/toggle-active', [RequirementDefinitionController::class, 'toggleActive'])->name('toggle-active');
        Route::delete('/{definition}', [RequirementDefinitionController::class, 'destroy'])->name('destroy');
    });

// Requirement Review
Route::prefix('requirements/review')
    ->name('requirements.review.')
    ->group(function () {
        Route::get('/', [RequirementReviewController::class, 'index'])->name('index');
        Route::get('/{student}', [RequirementReviewController::class, 'show'])->name('show');
        Route::patch('/submissions/{requirement}', [RequirementReviewController::class, 'updateSubmission'])->name('update-submission');
        Route::get('/submissions/{requirement}/file', [RequirementReviewController::class, 'file'])->name('file');
        Route::patch('/{student}/accept', [RequirementReviewController::class, 'acceptFieldStudy'])->name('accept');
        Route::patch('/{student}/reject', [RequirementReviewController::class, 'rejectFieldStudy'])->name('reject');
    

    });
            // Field Study Completion Requests (Coordinator)
        Route::prefix('field-study-requests')
            ->name('field-study-requests.')
            ->group(function () {

                Route::get('/', [CoordinatorFieldStudyRequestController::class, 'index'])
                    ->name('index');

                Route::patch('/{fieldStudyRequest}/approve', [CoordinatorFieldStudyRequestController::class, 'approve'])
                    ->name('approve');

                Route::patch('/{fieldStudyRequest}/reject', [CoordinatorFieldStudyRequestController::class, 'reject'])
                    ->name('reject');
            });
     });

/*
|--------------------------------------------------------------------------
| Supervisor Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/supervisor/dashboard', [SupervisorDashboardController::class, 'index'])
    ->name('supervisor.dashboard');
    /*
|--------------------------------------------------------------------------
| Supervisor Students
|--------------------------------------------------------------------------
*/

Route::get('/supervisor/students', [SupervisorStudentController::class, 'index'])
    ->name('supervisor.students.index');

    Route::get('/supervisor/students', [SupervisorStudentController::class, 'index'])
    ->name('supervisor.students.index');

Route::get('/supervisor/students/{student}', [SupervisorStudentController::class, 'show'])
    ->name('supervisor.students.show');

/*
|--------------------------------------------------------------------------
| Supervisor Observation
|--------------------------------------------------------------------------
*/


Route::prefix('supervisor')
    ->name('supervisor.')
    ->middleware(['auth']) // add your supervisor-specific middleware here too, if any
    ->group(function () {

        Route::prefix('observations')
            ->name('observations.')
            ->group(function () {

                // Observation List
                Route::get('/', [ObservationScheduleController::class, 'index'])
                    ->name('index');

                // Schedule Observation (form)
                Route::get('/create', [ObservationScheduleController::class, 'create'])
                    ->name('create');

                // Schedule Observation (submit)
                Route::post('/', [ObservationScheduleController::class, 'store'])
                    ->name('store');

                // View Observation
                Route::get('/{observation}', [ObservationScheduleController::class, 'show'])
                    ->name('show');

                // Edit Observation (form)
                Route::get('/{observation}/edit', [ObservationScheduleController::class, 'edit'])
                    ->name('edit');

                // Update Observation (submit)
                Route::put('/{observation}', [ObservationScheduleController::class, 'update'])
                    ->name('update');

                // Complete Observation
                Route::patch('/{observation}/complete', [ObservationScheduleController::class, 'complete'])
                    ->name('complete');

                // Cancel Observation
                Route::patch('/{observation}/cancel', [ObservationScheduleController::class, 'cancel'])
                    ->name('cancel');
            });
    });

/*
|--------------------------------------------------------------------------
| Supervisor Evaluations
|--------------------------------------------------------------------------
*/

Route::prefix('supervisor')
    ->name('supervisor.')
    ->middleware(['auth', 'role:supervisor'])
    ->group(function () {

        // Supervisor Evaluation
        Route::get(
            'observation-schedules/{observationSchedule}/students/{student}/evaluate',
            [SupervisorEvaluationController::class, 'create']
        )->name('evaluations.create');

        Route::post(
            'evaluations',
            [SupervisorEvaluationController::class, 'store']
        )->name('evaluations.store');

        Route::get(
            'evaluations/{evaluation}',
            [SupervisorEvaluationController::class, 'show']
        )->name('evaluations.show');

        Route::get(
            'evaluations/{evaluation}/edit',
            [SupervisorEvaluationController::class, 'edit']
        )->name('evaluations.edit');

        Route::put(
            'evaluations/{evaluation}',
            [SupervisorEvaluationController::class, 'update']
        )->name('evaluations.update');

        Route::get(
            'evaluations',
            [SupervisorEvaluationController::class, 'index']
        )->name('evaluations.index');


        // Other Evaluator Results
        Route::prefix('evaluations/{evaluation}/other-evaluator-results')
            ->name('evaluations.other-evaluator-results.')
            ->group(function () {

                Route::get(
                    '/',
                    [OtherEvaluatorResultController::class, 'index']
                )->name('index');

                Route::post(
                    '/',
                    [OtherEvaluatorResultController::class, 'store']
                )->name('store');
            });


        // Individual Other Evaluator Result
        Route::prefix('other-evaluator-results/{otherEvaluatorResult}')
            ->name('other-evaluator-results.')
            ->group(function () {

                Route::get(
                    '/',
                    [OtherEvaluatorResultController::class, 'show']
                )->name('show');

                // POST + _method=PUT for multipart file upload
                Route::post(
                    '/',
                    [OtherEvaluatorResultController::class, 'update']
                )->name('update');

                Route::delete(
                    '/',
                    [OtherEvaluatorResultController::class, 'destroy']
                )->name('destroy');
            });


                // Field Study Completion Requests
        Route::prefix('field-study-requests')
            ->name('field-study-requests.')
            ->group(function () {

                Route::get(
                    '/',
                    [FieldStudyRequestController::class, 'index']
                )->name('index');

                Route::patch(
                    '/{fieldStudyRequest}/approve',
                    [FieldStudyRequestController::class, 'approve']
                )->name('approve');

                Route::patch(
                    '/{fieldStudyRequest}/reject',
                    [FieldStudyRequestController::class, 'reject']
                )->name('reject');
            });
    });

/*
|--------------------------------------------------------------------------
| Student Dashboard & School Selection
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:student'])->group(function () {

    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])
        ->name('student.dashboard');

    Route::get('/student/deployment/select-school', [StudentDeploymentController::class, 'index'])
        ->name('student.deployment.select');

    Route::post('/student/deployment/select-school', [StudentDeploymentController::class, 'store'])
        ->name('student.deployment.store');

    // Field Study
    Route::get('/student/field-study', [FieldStudyController::class, 'index'])
        ->name('student.field-study');

    // Field Study Requirements
    Route::get('/student/field-study/requirements', [RequirementController::class, 'index'])
        ->name('student.field-study.requirements');

    Route::post('/student/field-study/requirements', [RequirementController::class, 'store'])
        ->name('student.field-study.requirements.store');

    Route::get('/student/field-study/requirements/{requirement}/file', [RequirementController::class, 'show'])
        ->name('student.field-study.requirements.file');

    // Field Study Completion Request
    Route::post(
    '/student/field-study/request-completion',
    [StudentFieldStudyRequestController::class, 'store']
)->name('student.field-study.completion-request.store');

// Add this import at the top of web.php with your other controller imports:


// DailyLogController

    Route::get('/student/teaching-hours', [DailyLogController::class, 'index'])
        ->name('student.teaching-hours');

    Route::post('/student/teaching-hours/time-in', [DailyLogController::class, 'timeIn'])
        ->name('student.teaching-hours.time-in');

    Route::post('/student/teaching-hours/time-out', [DailyLogController::class, 'timeOut'])
        ->name('student.teaching-hours.time-out');
});



/*
|--------------------------------------------------------------------------
| Redirect After Login
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->get('/dashboard', function () {

    return match (auth()->user()->role) {

        'admin'       => redirect()->route('admin.dashboard'),

        'coordinator' => redirect()->route('coordinator.dashboard'),

        'supervisor'  => redirect()->route('supervisor.dashboard'),

        'student'     => redirect()->route('student.dashboard'),

        default       => abort(403),
    };

})->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Student Dashboard, Profile & School Selection
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:student'])->group(function () {

    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])
        ->name('student.dashboard');

    Route::get('/student/profile', [StudentProfileController::class, 'index'])
        ->name('student.profile');

    Route::get('/student/deployment/select-school', [StudentDeploymentController::class, 'index'])
        ->name('student.deployment.select');

    Route::post('/student/deployment/select-school', [StudentDeploymentController::class, 'store'])
        ->name('student.deployment.store');
});

require __DIR__.'/auth.php';