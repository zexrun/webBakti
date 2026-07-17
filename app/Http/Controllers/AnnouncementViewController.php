<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;

class AnnouncementViewController extends Controller
{
    public function index()
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

        return view('announcements.index', compact('announcements'));
    }

    public function show(Announcement $announcement)
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

        return view('announcements.show', compact('announcement'));
    }
}
