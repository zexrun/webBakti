<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\User;

class AdminSidebarComposer
{
    /**
     * Bind data to the view untuk admin sidebar.
     */
    public function compose(View $view): void
    {
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            // Buat statistik untuk admin dashboard
            $adminStats = [
                'total_students' => Student::count(),
                'total_supervisors' => Supervisor::count(),
                'active_internships' => Student::whereNotNull('start_date')
                    ->whereNull('end_date')
                    ->count(),
                'completed_internships' => Student::whereNotNull('end_date')->count(),
                'pending_plotting' => Student::whereNull('supervisor_id')->count(),
                'users_by_role' => [
                    'students' => User::whereHas('roles', function ($q) {
                        $q->where('name', 'student');
                    })->count(),
                    'supervisors' => User::whereHas('roles', function ($q) {
                        $q->where('name', 'supervisor');
                    })->count(),
                    'admins' => User::whereHas('roles', function ($q) {
                        $q->where('name', 'admin');
                    })->count(),
                ]
            ];

            // Share data ke view
            $view->with('adminStats', $adminStats);
        }
    }
}
