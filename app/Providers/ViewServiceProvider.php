<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\View\Composers\StudentSidebarComposer;
use App\View\Composers\SupervisorSidebarComposer;
use App\View\Composers\AdminSidebarComposer;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // View Composer untuk Student
        // Sesuaikan dengan nama layout/view yang Anda gunakan
        View::composer([
            'layouts.app',          // Jika ada layout khusus student
            'partials.student-sidebar', // Jika sidebar student terpisah
            'student.*',               // Semua view di folder student
        ], StudentSidebarComposer::class);

        // View Composer untuk Supervisor  
        View::composer([
            'layouts.app',
            'partials.supervisor-sidebar', 
            'supervisor.*',
        ], SupervisorSidebarComposer::class);

        // View Composer untuk Admin
        View::composer([
            'layouts.app',
            'partials.admin-sidebar',
            'admin.*', 
        ], AdminSidebarComposer::class);

        // Jika menggunakan satu layout untuk semua (app.blade.php)
        // View::composer('layouts.app', [
        //     StudentSidebarComposer::class,
        //     SupervisorSidebarComposer::class,
        //     AdminSidebarComposer::class
        // ]);
    }
}