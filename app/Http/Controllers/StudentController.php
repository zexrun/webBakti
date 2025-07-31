<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Support\Facades\Auth;



class StudentController extends Controller
{
    public function dashboard()
    {
        return view('student.dashboard');
    }
}
