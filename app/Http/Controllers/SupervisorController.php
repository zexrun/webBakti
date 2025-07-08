<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupervisorController extends Controller
{
    public function dashboard()
    {
        return view('supervisor.dashboard');
    }
}
