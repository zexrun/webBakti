<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('admin')
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'priority' => 'required|in:low,normal,high,urgent',
            'target_roles' => 'required|array|min:1',
            'target_roles.*' => 'in:admin,supervisor,student',
            'publish_now' => 'nullable|boolean',
        ]);

        Announcement::create([
            'admin_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'priority' => $request->priority,
            'target_roles' => $request->target_roles,
            'published_at' => $request->has('publish_now') ? now() : null,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dibuat');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'priority' => 'required|in:low,normal,high,urgent',
            'target_roles' => 'required|array|min:1',
            'target_roles.*' => 'in:admin,supervisor,student',
            'publish_now' => 'nullable|boolean',
        ]);

        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
            'priority' => $request->priority,
            'target_roles' => $request->target_roles,
            'published_at' => $request->has('publish_now') && !$announcement->published_at ? now() : $announcement->published_at,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui');
    }

    public function publish(Announcement $announcement)
    {
        if (!$announcement->published_at) {
            $announcement->publish();
        }

        return redirect()->back()->with('success', 'Pengumuman berhasil dipublikasikan');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus');
    }
}
