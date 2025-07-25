@extends('layouts.app')
@section('title', 'Dashboard Utama')

@section('content')

<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">
        <!-- Header -->
        <div class="flex justify-between items-center px-6 py-5 bg-gradient-to-r from-gray-800 to-gray-700 text-white">
            <h4 class="text-xl font-semibold tracking-wide">👨‍🏫 Dashboard Pembimbing</h4>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-md text-sm font-medium shadow transition">
                    Logout
                </button>
            </form>
        </div>

        <!-- Body -->
        <div class="px-8 py-8">
            <h5 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang, {{ Auth::user()->name }}! 👋</h5>
            <p class="text-gray-600 text-sm mb-8">Anda login sebagai Pembimbing. Berikut menu untuk mengelola sistem.</p>

            <!-- Menu Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Penugasan -->
                <a href="{{ route('supervisor.tasks.index') }}" class="bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-xl p-5 shadow-sm transition transform hover:-translate-y-1">
                    <div class="text-3xl mb-3">📋</div>
                    <div class="text-lg font-semibold text-indigo-700">Lihat Penugasan</div>
                    <p class="text-sm text-gray-600 mt-1">Kelola dan pantau tugas yang sudah dibuat.</p>
                </a>

                <!-- Buat Tugas -->
                <a href="{{ route('supervisor.tasks.create') }}" class="bg-green-50 hover:bg-green-100 border border-green-200 rounded-xl p-5 shadow-sm transition transform hover:-translate-y-1">
                    <div class="text-3xl mb-3">✏️</div>
                    <div class="text-lg font-semibold text-green-700">Buat Tugas</div>
                    <p class="text-sm text-gray-600 mt-1">Tambahkan penugasan baru untuk mahasiswa.</p>
                </a>

                <!-- Mahasiswa -->
                <a href="{{ route('supervisor.students.index') }}" class="bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-xl p-5 shadow-sm transition transform hover:-translate-y-1">
                    <div class="text-3xl mb-3">👨‍🎓</div>
                    <div class="text-lg font-semibold text-blue-700">Lihat Mahasiswa</div>
                    <p class="text-sm text-gray-600 mt-1">Daftar dan informasi mahasiswa bimbingan.</p>
                </a>

                <!-- Laporan Harian -->
                <a href="{{ route('supervisor.daily-reports.index') }}" class="bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-xl p-5 shadow-sm transition transform hover:-translate-y-1">
                    <div class="text-3xl mb-3">📅</div>
                    <div class="text-lg font-semibold text-purple-700">Laporan Harian</div>
                    <p class="text-sm text-gray-600 mt-1">Lihat aktivitas harian mahasiswa.</p>
                </a>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="p-4 bg-green-100 text-green-800 border border-green-300 rounded-md text-sm relative">
                    ✅ {{ session('success') }}
                    <button type="button" class="absolute top-2 right-3 text-lg" onclick="this.parentElement.remove()">×</button>
                </div>
            @endif

            <hr class="mt-10 border-gray-200">
        </div>
    </div>
</div>

@endsection
