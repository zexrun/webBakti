<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    public function inbox(): Response
    {
        $messages = Message::where('recipient_id', Auth::id())
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = Message::where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return Inertia::render('Messages/Inbox', compact('messages', 'unreadCount'));
    }

    public function sent(): Response
    {
        $messages = Message::where('sender_id', Auth::id())
            ->with('recipient')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Messages/Sent', compact('messages'));
    }

    public function show(Message $message): Response
    {
        if ($message->recipient_id !== Auth::id() && $message->sender_id !== Auth::id()) {
            abort(403);
        }

        if ($message->recipient_id === Auth::id() && !$message->is_read) {
            $message->markAsRead();
        }

        $conversation = Message::where(function ($q) use ($message) {
            $q->where('sender_id', $message->sender_id)
              ->where('recipient_id', $message->recipient_id);
        })->orWhere(function ($q) use ($message) {
            $q->where('sender_id', $message->recipient_id)
              ->where('recipient_id', $message->sender_id);
        })->with('sender', 'recipient')
         ->orderBy('created_at', 'asc')
         ->get();

        return Inertia::render('Messages/Show', compact('message', 'conversation'));
    }

    public function create(): Response
    {
        $recipients = User::whereIn('role', ['supervisor', 'student', 'admin'])
            ->where('id', '!=', Auth::id())
            ->get();

        return Inertia::render('Messages/Create', compact('recipients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id|different:sender_id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
        ], [
            'recipient_id.different' => 'Tidak bisa mengirim pesan ke diri sendiri',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $request->recipient_id,
            'subject' => $request->subject,
            'body' => $request->body,
        ]);

        return redirect()->route('messages.sent')
            ->with('success', 'Pesan berhasil dikirim');
    }

    public function reply(Request $request, Message $message)
    {
        if ($message->recipient_id !== Auth::id() && $message->sender_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $senderId = Auth::id();
        $recipientId = $senderId === $message->sender_id ? $message->recipient_id : $message->sender_id;

        Message::create([
            'sender_id' => $senderId,
            'recipient_id' => $recipientId,
            'subject' => 'Re: ' . $message->subject,
            'body' => $request->body,
        ]);

        return redirect()->route('messages.show', $message->id)
            ->with('success', 'Balasan berhasil dikirim');
    }

    public function delete(Message $message)
    {
        if ($message->recipient_id !== Auth::id() && $message->sender_id !== Auth::id()) {
            abort(403);
        }

        $message->delete();

        return redirect()->back()->with('success', 'Pesan berhasil dihapus');
    }

    public function markAsRead(Message $message)
    {
        if ($message->recipient_id !== Auth::id()) {
            abort(403);
        }

        $message->markAsRead();

        return redirect()->back();
    }

    public function markAllAsRead()
    {
        Message::where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Semua pesan sudah ditandai sebagai terbaca');
    }
}
