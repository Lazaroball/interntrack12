<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentManagementController extends Controller
{
    /**
     * Display student management page.
     */
    public function index(Request $request)
    {
        $query = Student::with([
            'user',
            'deployment',
            'deployment.partnerSchool',
            'deployment.supervisor',
            'deployment.coordinator',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        | Search by:
        | - Student Number
        | - Reference Number
        | - First Name
        | - Last Name
        | - Full Name
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('student_number', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereRaw(
                        "CONCAT(first_name,' ',last_name) LIKE ?",
                        ["%{$search}%"]
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        if ($request->filled('program')) {
            $query->where('program', $request->program);
        }

        if ($request->filled('program_type')) {
            $query->where('program_type', $request->program_type);
        }

        if ($request->filled('block')) {
            $query->where('block', $request->block);
        }

        if (
            $request->filled('is_eligible')
            && in_array($request->is_eligible, ['1', '0'])
        ) {
            $query->where(
                'is_eligible',
                $request->is_eligible
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Deployment Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('deployment_status')) {
            if ($request->deployment_status === 'not_deployed') {
                $query->doesntHave('deployment');
            } else {
                $query->whereHas(
                    'deployment',
                    fn ($d) =>
                    $d->where(
                        'status',
                        $request->deployment_status
                    )
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        match ($request->get('sort', 'newest')) {
            'oldest'
                => $query->oldest(),

            'alphabetical'
                => $query
                    ->orderBy('last_name')
                    ->orderBy('first_name'),

            'student_number'
                => $query->orderBy('student_number'),

            default
                => $query->latest(),
        };

        $students = $query
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Filter Options
        |--------------------------------------------------------------------------
        */

       $programs = Student::whereNotNull('program')
    ->where('program', '!=', '')
    ->distinct()
    ->orderBy('program')
    ->pluck('program');

        $programTypes = [
            'Field Study',
            'Internship',
        ];

       $blocks = Student::query()
    ->select('block')
    ->whereNotNull('block')
    ->where('block', '!=', '')
    ->distinct()
    ->orderBy('block')
    ->pluck('block');

        $deploymentStatuses = [
            'not_deployed',
            'pending',
            'deployed',
            'completed',
        ];

        /*
        |--------------------------------------------------------------------------
        | Hour Targets
        |--------------------------------------------------------------------------
        */

        $fieldStudyTarget = 600;
        $internshipTarget = 600;

        /*
        |--------------------------------------------------------------------------
        | Dashboard Cards
        |--------------------------------------------------------------------------
        */

        $counts = [
            'total'
                => Student::count(),

            'field_study'
                => Student::where(
                    'program_type',
                    'Field Study'
                )->count(),

            'internship'
                => Student::where(
                    'program_type',
                    'Internship'
                )->count(),

            'eligible'
                => Student::where(
                    'is_eligible',
                    true
                )->count(),

            'pending_requirements'
                => Student::where(
                    'is_eligible',
                    false
                )->count(),

            'deployed'
                => Student::whereHas(
                    'deployment',
                    fn ($d)
                    => $d->where(
                        'status',
                        'deployed'
                    )
                )->count(),
        ];

        return view(
            'coordinator.students.index',
            compact(
                'students',
                'programs',
                'programTypes',
                'blocks',
                'deploymentStatuses',
                'counts',
                'fieldStudyTarget',
                'internshipTarget'
            )
        );
    }

    /**
     * Display a single student's profile.
     */
    public function show(Student $student)
    {
        $student->load([
            'user',
            'deployment',
            'deployment.partnerSchool',
            'deployment.supervisor',
            'deployment.coordinator',
        ]);

        $fieldStudyTarget = 600;
        $internshipTarget = 600;

        $fieldStudyPercent =
            $fieldStudyTarget > 0
                ? min(
                    round(
                        ($student->field_study_hours / $fieldStudyTarget) * 100
                    ),
                    100
                )
                : 0;

        $internshipPercent =
            $internshipTarget > 0
                ? min(
                    round(
                        ($student->internship_hours / $internshipTarget) * 100
                    ),
                    100
                )
                : 0;

        return view(
            'coordinator.students.show',
            compact(
                'student',
                'fieldStudyTarget',
                'internshipTarget',
                'fieldStudyPercent',
                'internshipPercent'
            )
        );
    }
}