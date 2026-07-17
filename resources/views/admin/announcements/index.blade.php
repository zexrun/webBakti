@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Pengumuman</h1>
            <p class="text-gray-600">Kelola pengumuman sistem</p>
        </div>
        <a href="{{ route('admin.announcements.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
            + Buat Pengumuman
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Announcements List -->
    <div class="space-y-4">
        @forelse($announcements as $announcement)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $announcement->title }}</h3>
                            <span class="px-2 py-1 rounded text-xs font-medium
                                @if($announcement->priority === 'urgent') bg-red-100 text-red-800
                                @elseif($announcement->priority === 'high') bg-orange-100 text-orange-800
                                @elseif($announcement->priority === 'normal') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($announcement->priority) }}
                            </span>
                            @if($announcement->published_at)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">Dipublikasikan</span>
                            @else
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-medium">Draft</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600">Oleh {{ $announcement->admin->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">{{ $announcement->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>

                <p class="text-gray-700 mb-4 line-clamp-3">{{ $announcement->content }}</p>

                <div class="flex gap-3">
                    <a href="{{ route('admin.announcements.edit', $announcement) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                        Edit
                    </a>
                    @if(!$announcement->published_at)
                        <form action="{{ route('admin.announcements.publish', $announcement) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-800 font-medium text-sm">
                                Publikasikan
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengumuman ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg p-12 text-center border border-gray-200">
                <p class="text-gray-600">Belum ada pengumuman</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $announcements->links() }}
    </div>
</div>
@endsection
