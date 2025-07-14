@extends('layouts.supervisor')
@section('title', 'Dashboard Utama')

@section('content')

<div class="max-w-4xl mx-auto px-4 py-10">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-gray-800 text-white px-6 py-4 flex justify-between items-center">
            <h4 class="text-lg font-semibold">Dashboard Pembimbing</h4>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition">
                    Logout
                </button>
            </form>
        </div>

        <div class="px-6 py-5">
            <h5 class="text-xl font-semibold mb-2">Selamat Datang, {{ Auth::user()->name }}!</h5>
            <p class="text-gray-600 mb-6">Anda login sebagai Pembimbing. Gunakan menu di bawah ini untuk mengelola sistem.</p>

            <div class="flex flex-wrap gap-4 mb-6">
                <a href="{{ route('supervisor.tasks.index') }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-md transition text-sm">
                   Lihat Penugasan
                </a>
                <a href="{{ route('supervisor.tasks.create') }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md transition text-sm">
                   Buat Tugas
                </a>
                <a href="{{ route('supervisor.students.index') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md transition text-sm">
                   Lihat Mahasiswa
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg relative">
                    {{ session('success') }}
                    <button type="button" class="absolute top-2 right-3 text-lg" onclick="this.parentElement.remove()">×</button>
                </div>
            @endif

            <hr class="mt-6 border-t border-gray-200">
        </div>
    </div>
</div>

@endsection
