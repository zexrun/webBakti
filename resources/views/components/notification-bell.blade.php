@php
    $unreadNotifications = auth()->user()->unreadNotifications()->latest()->get();
    $unreadCount = $unreadNotifications->count();
@endphp

<div class="relative" x-data="{ open: false }">
    <!-- Bell Button -->
    <button
        @click="open = !open"
        class="relative p-2 text-gray-600 hover:text-gray-900 transition-colors"
        title="Notifikasi">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        <!-- Unread Badge -->
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Menu -->
    <div
        x-show="open"
        @click.outside="open = false"
        x-transition
        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50 max-h-96 overflow-y-auto">

        <!-- Header -->
        <div class="sticky top-0 bg-gray-50 border-b border-gray-200 p-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Notifikasi</h3>
            @if($unreadCount > 0)
                <button
                    @click="
                        fetch('{{ route('notifications.mark-all-as-read') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                            }
                        }).then(() => window.location.reload());
                    "
                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                    Tandai semua dibaca
                </button>
            @endif
        </div>

        <!-- Notifications List -->
        @if($unreadNotifications->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($unreadNotifications as $notification)
                    <div class="p-4 hover:bg-gray-50 transition-colors cursor-pointer group"
                         @click="
                            fetch('{{ route('notifications.mark-as-read', $notification->id) }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                                }
                            }).then(() => {
                                @if(isset($notification->data['action_url']))
                                    window.location.href = '{{ $notification->data['action_url'] }}';
                                @endif
                            });
                         ">

                        <div class="flex items-start">
                            <!-- Icon based on notification type -->
                            <div class="flex-shrink-0 mt-0.5">
                                @if($notification->data['type'] === 'new_task')
                                    <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                        </svg>
                                    </div>
                                @elseif($notification->data['type'] === 'submission_graded')
                                    <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-100">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @elseif(str_starts_with($notification->data['type'], 'task_deadline'))
                                    <div class="flex items-center justify-center h-8 w-8 rounded-full bg-yellow-100">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="flex items-center justify-center h-8 w-8 rounded-full bg-gray-100">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $notification->data['message'] ?? 'Notifikasi' }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            <!-- Delete button -->
                            <button
                                @click.stop="
                                    fetch('{{ route('notifications.delete', $notification->id) }}', {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
                                        }
                                    }).then(() => window.location.reload());
                                "
                                class="ml-2 text-gray-400 hover:text-gray-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="text-gray-500 text-sm">Tidak ada notifikasi</p>
            </div>
        @endif

        <!-- View All Link -->
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 p-4">
            <a href="{{ route('notifications.index') }}" class="text-center block text-sm text-blue-600 hover:text-blue-800 font-medium">
                Lihat Semua Notifikasi
            </a>
        </div>
    </div>
</div>
