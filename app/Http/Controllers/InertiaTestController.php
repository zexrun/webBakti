<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class InertiaTestController extends Controller
{
    public function dashboard(): Response
    {
        return Inertia::render('Dashboard', [
            'user' => auth()->user(),
        ]);
    }
}
