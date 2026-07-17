@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Pengumuman</h1>
        <p class="text-gray-600">Daftar pengumuman untuk Anda</p>
    </div>

    @if($announcements->isEmpty())
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-8 text-center">
            <p class="text-blue-800 text-lg">📢 Belum ada pengumuman</p>
            <p class="text-blue-600 text-sm mt-2">Pengumuman baru akan ditampilkan di sini</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($announcements as $announcement)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <a href="{{ route('announcements.show', $announcement) }}" class="text-lg font-semibold text-gray-900 hover:text-blue-600">
                                {{ $announcement->title }}
                            </a>
                        </div>
                        <span class="
                            px-3 py-1 rounded-full text-xs font-medium ml-4 flex-shrink-0
                            {{ $announcement->priority === 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $announcement->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                            {{ $announcement->priority === 'normal' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $announcement->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}
                        ">
                            {{ ucfirst($announcement->priority) }}
                        </span>
                    </div>

                    <!-- Content Preview -->
                    <p class="text-gray-600 text-sm line-clamp-2 mb-4">
                        {{ Str::limit(strip_tags($announcement->content), 200) }}
                    </p>

                    <!-- Meta Info -->
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <div class="flex items-center space-x-4">
                            <span>📅 {{ $announcement->published_at->format('d M Y') }}</span>
                            <span>🕐 {{ $announcement->published_at->format('H:i') }}</span>
                        </div>
                        <a href="{{ route('announcements.show', $announcement) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            Baca Selengkapnya →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $announcements->links() }}
        </div>
    @endif
</div>
@endsection
