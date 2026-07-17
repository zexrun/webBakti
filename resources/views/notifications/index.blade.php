@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Notifikasi</h1>
                <p class="text-gray-600 mt-1">Kelola semua notifikasi Anda</p>
            </div>
            @if(auth()->user()->notifications()->exists())
                <form action="{{ route('notifications.delete-all') }}" method="POST" onsubmit="return confirm('Hapus semua notifikasi?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 border border-red-300 rounded-md hover:bg-red-50 transition-colors">
                        Hapus Semua
                    </button>
                </form>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="space-y-4">
            @forelse($notifications as $notification)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex items-start hover:shadow-md transition-shadow">
                    <!-- Icon -->
                    <div class="flex-shrink-0 mt-1">
                        @if($notification->data['type'] === 'new_task')
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </div>
                        @elseif($notification->data['type'] === 'submission_graded')
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-green-100">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        @elseif(str_starts_with($notification->data['type'], 'task_deadline'))
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-yellow-100">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        @elseif(str_starts_with($notification->data['type'], 'attendance_'))
                            <div class="flex items-center justify-center h-10 w-10 rounded-full {{ str_ends_with($notification->data['type'], 'approved') ? 'bg-green-100' : 'bg-red-100' }}">
                                <svg class="w-6 h-6 {{ str_ends_with($notification->data['type'], 'approved') ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        @elseif(str_starts_with($notification->data['type'], 'exception_'))
                            <div class="flex items-center justify-center h-10 w-10 rounded-full {{ str_ends_with($notification->data['type'], 'approved') ? 'bg-green-100' : 'bg-red-100' }}">
                                <svg class="w-6 h-6 {{ str_ends_with($notification->data['type'], 'approved') ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        @else
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-gray-100">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="ml-4 flex-1">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-base font-semibold text-gray-900">
                                    {{ $notification->data['message'] ?? 'Notifikasi' }}
                                </p>
                                <p class="mt-2 text-sm text-gray-600">
                                    {{ $notification->created_at->format('d M Y H:i') }}
                                </p>

                                @if(isset($notification->data['action_url']))
                                    <a href="{{ $notification->data['action_url'] }}"
                                       class="inline-block mt-3 px-3 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 transition-colors">
                                        Lihat Detail
                                    </a>
                                @endif
                            </div>

                            <!-- Status Badge -->
                            <div class="ml-4">
                                @if(!$notification->read_at)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Belum dibaca
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Priority Badge -->
                        @if(isset($notification->data['priority']))
                            <div class="mt-3">
                                @if($notification->data['priority'] === 'urgent')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Urgent
                                    </span>
                                @elseif($notification->data['priority'] === 'high')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        Penting
                                    </span>
                                @elseif($notification->data['priority'] === 'medium')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Normal
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Delete Button -->
                    <form action="{{ route('notifications.delete', $notification->id) }}" method="POST" class="ml-4">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-md transition-colors"
                                title="Hapus notifikasi">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </form>
                </div>
            @empty
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada notifikasi</h3>
                    <p class="text-gray-500">Anda sudah membaca semua notifikasi Anda.</p>
                </div>
            @endforelse

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="mt-8">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
