@extends('layouts.app')
@section('title', 'List Mahasiswa')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Manajemen Pengguna</h1>
                    <p class="text-gray-600 mt-1">Kelola data mahasiswa dan pembimbing</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('admin.users.create') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah User
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session('danger'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ session('danger') }}
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Search Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari nama pengguna..." 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                    </div>
                </div>
                <button type="submit" 
                        class="inline-flex items-center justify-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Cari
                </button>
            </form>
        </div>

        <!-- Administrator Section -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-2 h-2 bg-blue-600 rounded-full mr-3"></div>
                        Administrator
                        <span class="ml-2 px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                            {{ $admins->total() }}
                        </span>
                    </h3>
                </div>
                @include('admin.users.partials.table', ['users' => $admins])
                @if($admins->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $admins->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Pembimbing Section -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-2 h-2 bg-green-600 rounded-full mr-3"></div>
                        Pembimbing
                        <span class="ml-2 px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                            {{ $supervisors->total() }}
                        </span>
                    </h3>
                </div>
                @include('admin.users.partials.table', ['users' => $supervisors])
                @if($supervisors->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $supervisors->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Mahasiswa Section -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-2 h-2 bg-purple-600 rounded-full mr-3"></div>
                        Mahasiswa
                        <span class="ml-2 px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">
                            {{ $students->total() }}
                        </span>
                    </h3>
                </div>
                @include('admin.users.partials.table', ['users' => $students])
                @if($students->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $students->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection