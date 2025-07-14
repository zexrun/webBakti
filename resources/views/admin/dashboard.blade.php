@extends('layouts.admin') {{-- atau x-app-layout --}}

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Admin</h1>
        <p class="text-lg text-gray-600">Selamat Datang kembali, {{ Auth::user()->name }}!</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div class="bg-blue-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-2.308M15 19.128v-3.863M15 19.128A9.37 9.37 0 0 1 12 21a9.37 9.37 0 0 1-3-5.872M9 9.128a9.38 9.38 0 0 0 2.625.372A9.337 9.337 0 0 0 15.75 7.25m-6.75 1.878A9.37 9.37 0 0 1 12 5.25a9.37 9.37 0 0 1 3 5.872m0 0a9.38 9.38 0 0 0 2.625.372m-11.625.372A9.38 9.38 0 0 0 3.375 7.25m11.625 1.878c0 2.118-2.684 3.878-6.75 3.878S5.25 11.246 5.25 9.128" />
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-gray-600">Total Mahasiswa</p>
                <p class="text-2xl font-bold text-gray-800">{{ $studentCount }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div class="bg-green-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75a17.933 17.933 0 0 1-7.499-1.632Z" />
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-gray-600">Total Pembimbing</p>
                <p class="text-2xl font-bold text-gray-800">{{ $supervisorCount }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <div class="bg-yellow-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-gray-600">Total Tugas Dibuat</p>
                <p class="text-2xl font-bold text-gray-800">{{ $taskCount }}</p>
            </div>
        </div>
    </div>

    <div>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Menu Utama</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <a href="{{ route('admin.users.index') }}" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                <h5 class="text-xl font-bold text-gray-900">Manajemen Pengguna</h5>
                <p class="mt-2 text-gray-600">Tambah, edit, atau hapus data mahasiswa dan pembimbing.</p>
            </a>
            <a href="{{ route('admin.plotting') }}" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                <h5 class="text-xl font-bold text-gray-900">Plotting Pembimbing</h5>
                <p class="mt-2 text-gray-600">Tugaskan mahasiswa ke pembimbing yang sesuai.</p>
            </a>
            </div>
    </div>
@endsection