<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class SupervisorSidebarComposer
{
    /**
     * Bind data to the view untuk supervisor sidebar.
     */
    public function compose(View $view): void
    {
        if (Auth::check() && Auth::user()->hasRole('supervisor')) {
            // Ambil data supervisor dengan relasi students
            $supervisor = Auth::user()->supervisor()->with([
                'students.user', 
                'students.finalAssessment'
            ])->first();
            
            // Buat statistik untuk supervisor
            $supervisorStats = null;
            if ($supervisor) {
                $supervisorStats = [
                    'total_students' => $supervisor->students()->count(),
                    'completed_assessments' => $supervisor->students()->whereHas('finalAssessment')->count(),
                    'pending_assessments' => $supervisor->students()->whereDoesntHave('finalAssessment')->count(),
                ];
            }
            
            // Share data ke view
            $view->with([
                'supervisor' => $supervisor,
                'supervisorStats' => $supervisorStats
            ]);
        }
    }
}