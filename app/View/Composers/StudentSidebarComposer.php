<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class StudentSidebarComposer
{
    /**
     * Bind data to the view untuk student sidebar.
     */
    public function compose(View $view): void
    {
        // Hanya untuk user dengan role student
        if (Auth::check() && Auth::user()->hasRole('student')) {
            // Ambil data student dengan relasi finalAssessment
            $student = Auth::user()->student()->with(['finalAssessment'])->first();
            
            // Share variable student ke view
            $view->with('student', $student);
        }
    }
}