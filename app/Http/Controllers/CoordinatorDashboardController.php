<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Deployment;
use App\Models\PartnerSchool;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CoordinatorDashboardController extends Controller
{
    public function index()
    {
        // Auth guard
        $user = Auth::user();
        $coordinator = $user->coordinator;

        // Statistics
        $stats = [
            'total_students'        => Student::count(),
            'partner_schools'       => PartnerSchool::count(),
            'active_deployments'    => Deployment::where('status', 'deployed')->count(),
            'pending_deployments'   => Deployment::where('status', 'pending')->count(),
            'completed_deployments' => Deployment::where('status', 'completed')->count(),
            'active_supervisors'    => User::where('role', 'supervisor')
                ->where('status', true)
                ->count(),
        ];

        // Recent activity
        $recentActivity = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        // IDs of currently soft-deleted Partner Schools
        $restorableSchoolIds = PartnerSchool::onlyTrashed()
            ->pluck('id')
            ->all();

        // Determine which activity entries can be undone
        $recentActivity->each(function ($log) use ($restorableSchoolIds) {
            $log->is_restorable =
                $log->module === 'Partner Schools'
                && $log->action === 'Deleted'
                && $log->subject_id !== null
                && in_array(
                    (int) $log->subject_id,
                    $restorableSchoolIds,
                    true
                );
        });

        return view('coordinator.dashboard', compact(
            'user',
            'coordinator',
            'stats',
            'recentActivity'
        ));
    }
}