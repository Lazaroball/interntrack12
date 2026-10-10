<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Deployment;
use App\Models\FieldStudyRequest;
use App\Models\ActivityLog;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the Admin Dashboard metrics.
     */
    public function index(): View
    {
        // Recent activity logs
       try {
    $recentActivity = ActivityLog::with('user')
        ->whereHas('user', fn ($q) => $q->where('role', 'admin'))
        ->latest()
        ->take(10)
        ->get();
} catch (\Exception $e) {
    report($e);
    $recentActivity = collect();
}

        return view('admin.dashboard', [
            'stats' => [

                // Total students
                'total_students' => Student::count(),

                // Total supervisors
                'total_supervisors' => User::where('role', 'supervisor')->count(),

                // Total coordinators
                'total_coordinators' => User::where('role', 'coordinator')->count(),

                // Active deployments
                'active_deployments' => Deployment::where('status', 'deployed')->count(),

                // Pending field study requests
                'pending_requests' => FieldStudyRequest::where('status', 'pending')->count(),

                // Completed internships
                'completed_internships' => Deployment::where('status', 'completed')->count(),
            ],

            // For recent activity section
            'recentActivity' => $recentActivity,
        ]);
    }
}