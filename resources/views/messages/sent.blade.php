@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Pesan Terkirim</h1>
            <p class="text-gray-600">Daftar pesan yang sudah anda kirim</p>
        </div>
        <a href="{{ route('messages.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
            + Buat Pesan
        </a>
    </div>

    <!-- Tabs -->
    <div class="flex gap-4 mb-6 border-b border-gray-200">
        <a href="{{ route('messages.inbox') }}" class="px-4 py-2 border-b-2 border-transparent text-gray-600 hover:text-gray-900 font-medium">
            Inbox
        </a>
        <a href="{{ route('messages.sent') }}" class="px-4 py-2 border-b-2 border-blue-600 text-blue-600 font-medium">
            Terkirim
        </a>
    </div>

    <!-- Messages List -->
    <div class="space-y-2">
        @forelse($messages as $message)
            <a href="{{ route('messages.show', $message) }}" class="block bg-white border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-semibold text-gray-900">Ke: {{ $message->recipient->name }}</h3>
                        </div>
                        <p class="text-sm font-medium text-gray-700 mb-1">{{ $message->subject }}</p>
                        <p class="text-sm text-gray-600 line-clamp-2">{{ substr($message->body, 0, 100) }}...</p>
                    </div>
                    <div class="text-right ml-4">
                        <p class="text-xs text-gray-500">
                            {{ $message->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-lg p-12 text-center border border-gray-200">
                <p class="text-gray-600">Belum ada pesan terkirim</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $messages->links() }}
    </div>
</div>
@endsection
