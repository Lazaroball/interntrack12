<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Supervisor;
use Illuminate\Http\Request;

class SupervisorStudentController extends Controller
{
    /**
     * Display students assigned to the logged-in supervisor.
     */
    public function index(Request $request)
    {
        $supervisor = Supervisor::where(
            'user_id',
            $request->user()->id
        )->firstOrFail();

        $deployments = $supervisor->deployments()
            ->with([
                'student',
                'partnerSchool',
            ])
            ->whereNull('completed_at')
            ->latest('deployment_date')
            ->get();

        return view(
            'supervisor.students.index',
            compact('supervisor', 'deployments')
        );
    }

    /**
     * Display a single student's profile, scoped to this supervisor's
     * own deployment record for that student.
     */
    public function show(Request $request, $studentId)
    {
        $supervisor = Supervisor::where(
            'user_id',
            $request->user()->id
        )->firstOrFail();

        // Scoped to $supervisor->deployments() so a supervisor can never
        // view a student who isn't actually assigned to them.
        $deployment = $supervisor->deployments()
            ->with(['student', 'partnerSchool'])
            ->where('student_id', $studentId)
            ->latest('deployment_date')
            ->firstOrFail();

        $student = $deployment->student;

        // TODO: these targets are placeholders. Replace with whatever
        // source of truth the coordinator side uses (config value,
        // settings table, etc.) so both dashboards show the same numbers.
        $fieldStudyTarget = 240;
        $internshipTarget = 500;

        $fieldStudyPercent = $fieldStudyTarget > 0
            ? min(100, round((($student->field_study_hours ?? 0) / $fieldStudyTarget) * 100))
            : 0;

        $internshipPercent = $internshipTarget > 0
            ? min(100, round((($student->internship_hours ?? 0) / $internshipTarget) * 100))
            : 0;

        return view('supervisor.students.show', compact(
            'student',
            'deployment',
            'fieldStudyTarget',
            'internshipTarget',
            'fieldStudyPercent',
            'internshipPercent'
        ));
    }
}