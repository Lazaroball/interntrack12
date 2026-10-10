<?php

namespace App\Http\Controllers;
use App\Models\ObservationSchedule;
use App\Models\Supervisor;
use Illuminate\Http\Request;

class SupervisorDashboardController extends Controller
{
    /**
     * Supervisor Dashboard.
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Supervisor
        |--------------------------------------------------------------------------
        */

        $supervisor = Supervisor::where(
            'user_id',
            $request->user()->id
        )->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Assigned Students
        |--------------------------------------------------------------------------
        |
        | A student is considered assigned to this supervisor when the
        | deployment contains this supervisor's ID.
        |
        */

        $assignedStudents = $supervisor->deployments()
            ->with([
                'student',
                'partnerSchool',
            ])
            ->whereNull('completed_at')
            ->latest('deployment_date')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Assigned Students Count
        |--------------------------------------------------------------------------
        */

        $assignedStudentsCount = $assignedStudents->count();

        /*
        |--------------------------------------------------------------------------
        | Recent Assignments
        |--------------------------------------------------------------------------
        |
        | Show the most recent students assigned to this supervisor.
        |
        */

        $recentAssignments = $assignedStudents
            ->take(5)
            ->map(function ($deployment) {

                $studentName = 'Unknown Student';

                if ($deployment->student) {
                    $studentName = trim(
                        $deployment->student->first_name . ' ' .
                        $deployment->student->last_name
                    );
                }

                return [
                    'id' => $deployment->id,

                    'name' => $studentName,

                    'program' => $deployment->program,

                    'school' => $deployment->partnerSchool
                        ? $deployment->partnerSchool->school_name
                        : 'No Partner School',

                    'deployment_date' => $deployment->deployment_date,

                    'status' => $deployment->status,
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Observation Data
        |--------------------------------------------------------------------------
        |
        | Observation schedules are not implemented yet.
        | We intentionally do NOT create fake counts here.
        |
        */

        $pendingObservationsCount = ObservationSchedule::where('supervisor_id', $supervisor->id)
    ->scheduled()
    ->count();

$completedObservationsCount = ObservationSchedule::where('supervisor_id', $supervisor->id)
    ->completed()
    ->count();

        /*
        |--------------------------------------------------------------------------
        | Recent Activity
        |--------------------------------------------------------------------------
        |
        | Until the Observation and Flying Visit modules exist,
        | use actual deployment activity instead of placeholder text.
        |
        */

        $recentActivity = $assignedStudents
            ->take(5)
            ->map(function ($deployment) {

                $studentName = 'Unknown Student';

                if ($deployment->student) {
                    $studentName = trim(
                        $deployment->student->first_name . ' ' .
                        $deployment->student->last_name
                    );
                }

                return [
                    'type' => 'assigned',

                    'text' => 'Assigned student: ' . $studentName,

                    'time' => $deployment->deployment_date
                        ? $deployment->deployment_date->format('M d, Y')
                        : 'Recently',
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        return view('supervisor.dashboard', compact(
            'supervisor',
            'assignedStudents',
            'assignedStudentsCount',
            'recentAssignments',
            'pendingObservationsCount',
            'completedObservationsCount',
            'recentActivity'
        ));
    }
}