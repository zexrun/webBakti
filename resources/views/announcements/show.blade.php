@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('announcements.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
            ← Kembali ke Pengumuman
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $announcement->title }}</h1>
        <div class="flex items-center justify-between text-sm text-gray-500">
            <div class="flex items-center space-x-4">
                <span>📅 {{ $announcement->published_at->format('d M Y H:i') }}</span>
                <span>👤 {{ $announcement->admin->name ?? 'Admin' }}</span>
                <span class="
                    px-3 py-1 rounded-full text-xs font-medium
                    {{ $announcement->priority === 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                    {{ $announcement->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                    {{ $announcement->priority === 'normal' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $announcement->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}
                ">
                    {{ ucfirst($announcement->priority) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 prose prose-sm max-w-none">
        {!! nl2br(e($announcement->content)) !!}
    </div>

    <!-- Footer -->
    <div class="mt-8 text-center">
        <a href="{{ route('announcements.index') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
            Kembali ke Daftar Pengumuman
        </a>
    </div>
</div>
@endsection
