<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementViewController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();
        $roleMap = [
            'admin' => 'admin',
            'supervisor' => 'supervisor',
            'student' => 'student',
        ];

        $userRole = $roleMap[$user->role] ?? null;

        $announcements = Announcement::where('published_at', '!=', null)
            ->whereJsonContains('target_roles', $userRole)
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        return Inertia::render('Announcements/Index', compact('announcements'));
    }

    public function show(Announcement $announcement): Response
    {
        $user = Auth::user();
        $roleMap = [
            'admin' => 'admin',
            'supervisor' => 'supervisor',
            'student' => 'student',
        ];

        $userRole = $roleMap[$user->role] ?? null;

        if (!in_array($userRole, $announcement->target_roles ?? [])) {
            abort(403, 'Anda tidak memiliki akses ke pengumuman ini');
        }

        if (!$announcement->published_at) {
            abort(404, 'Pengumuman tidak ditemukan');
        }

        return Inertia::render('Announcements/Show', compact('announcement'));
    }
}
