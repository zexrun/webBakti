<?php

namespace App\Http\Controllers;

use App\Models\Supervisor;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SupervisorController extends Controller
{
    public function dashboard()
    {
        return view('supervisor.dashboard');
    }

    public function viewStudent()
    {
        $supervisor = Supervisor::where('user_id', Auth::id())->firstOrFail();

        $students = $supervisor->students()->with('user')->paginate(10);

        return view('supervisor.view-student', compact('students'));    
    }
}
