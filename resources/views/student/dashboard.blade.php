@extends('layouts.student')

@section('title', 'Student')

@section('content')

<div class="max-w-4xl mx-auto px-4 py-10">
    <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-200">
        <div class="flex items-center justify-between bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-4">
            <h4 class="text-lg font-semibold text-white">Dashboard Mahasiswa</h4>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-1.5 rounded-md transition">
                    Logout
                </button>
            </form>
        </div>
        <div class="px-6 py-6">
            <h5 class="text-xl font-bold text-gray-800 mb-2">Selamat Datang, {{ Auth::user()->name }}!</h5>
            <p class="text-gray-600 mb-6">Anda login sebagai Mahasiswa. Gunakan menu di bawah ini untuk mengelola sistem.</p>

            <a href="{{ route('student.tasks.index') }}"
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md shadow-sm transition">
                📚 Tugas Saya
            </a>
            <a href="{{ route('student.daily-reports.index') }}"
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md shadow-sm transition">
                📚 Laporan Harian
            </a>
            <a href="{{ route('student.info.edit') }}"
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md shadow-sm transition">
                📚 Informasi Akun
            </a>
            
            @if(session('success'))
                <div class="mt-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <hr class="my-6 border-gray-200">
        </div>
    </div>
</div>

@endsection
