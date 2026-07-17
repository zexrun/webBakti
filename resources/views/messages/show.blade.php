@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="{{ route('messages.inbox') }}" class="text-blue-600 hover:text-blue-800 font-medium mb-2 inline-block">
                ← Kembali ke Inbox
            </a>
        </div>
        <form action="{{ route('messages.delete', $message) }}" method="POST" onsubmit="return confirm('Hapus pesan ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                🗑️ Hapus
            </button>
        </form>
    </div>

    <!-- Conversation -->
    <div class="space-y-4 mb-8">
        @foreach($conversation as $msg)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $msg->sender->name }}</p>
                        <p class="text-sm text-gray-500">{{ $msg->created_at->format('d M Y H:i') }}</p>
                    </div>
                    @if($msg->recipient_id === Auth::id() && $msg->is_read)
                        <span class="text-xs text-gray-500">✓ Terbaca</span>
                    @endif
                </div>
                <p class="text-sm font-medium text-gray-700 mb-2">{{ $msg->subject }}</p>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $msg->body }}</p>
            </div>
        @endforeach
    </div>

    <!-- Reply Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Balas Pesan</h2>

        <form action="{{ route('messages.reply', $message) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Balasan</label>
                <textarea name="body" rows="5" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900" placeholder="Tulis balasan anda..." required></textarea>
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                Kirim Balasan
            </button>
        </form>
    </div>
</div>
@endsection
