<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationTestingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Admin-only debug panel: fire any notification class (in-app + email)
 * against a chosen user using real seeded data, without completing the
 * actual workflow (grading, approving an exception, etc). Exists purely
 * to speed up manually testing the notification bell/toast and the
 * email templates - never linked from anywhere else in the app.
 */
class NotificationTestController extends Controller
{
    public function index(NotificationTestingService $service): Response
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email', 'role']);

        $catalog = collect($service->catalog())
            ->map(fn ($entry, $key) => ['key' => $key, 'label' => $entry['label']])
            ->values();

        return Inertia::render('Admin/NotificationTest/Index', [
            'users' => $users,
            'catalog' => $catalog,
        ]);
    }

    public function send(Request $request, NotificationTestingService $service)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'notification_key' => 'required|string',
        ]);

        $recipient = User::findOrFail($validated['user_id']);
        $error = $service->send($validated['notification_key'], $recipient);

        if ($error) {
            return back()->with('error', $error);
        }

        return back()->with('success', "Notifikasi terkirim ke {$recipient->name}.");
    }
}
