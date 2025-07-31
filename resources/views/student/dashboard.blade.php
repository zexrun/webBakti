@extends('layouts.app') @section('title', 'Student') @section('content')

@php
    use Illuminate\Support\Str;
@endphp


<div class="base-div">
    <div
        class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100"
    >
        <!-- Header -->
        <div
            class="flex items-center justify-between bg-gradient-to-r from-blue-600 to-indigo-500 px-6 py-5"
        >
            <h4 class="text-xl font-semibold text-white tracking-wide">
                🎓 Dashboard Mahasiswa
            </h4>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-md shadow-md transition duration-200"
                >
                    Logout
                </button>
            </form>
        </div>

        <!-- Body -->
        <div class="px-8 py-8">
            <h5 class="text-2xl font-bold text-gray-800 mb-2">
                Halo, {{ Auth::user()->name }}! 👋
            </h5>
            <p class="text-gray-600 mb-8 text-sm">
                Kamu login sebagai Mahasiswa. Silakan pilih menu di bawah ini
                untuk mengakses fitur.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Card: Tugas -->
                <a
                    href="{{ route('student.tasks.index') }}"
                    class="bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-xl p-5 shadow-sm transition transform hover:-translate-y-1"
                >
                    <div class="text-3xl mb-3">📝</div>
                    <div class="text-lg font-semibold text-blue-700">
                        Tugas Saya
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        Lihat dan kerjakan tugas yang diberikan.
                    </p>
                </a>

                <!-- Card: Laporan Harian -->
                <a
                    href="{{ route('student.logbooks.index') }}"
                    class="bg-green-50 hover:bg-green-100 border border-green-200 rounded-xl p-5 shadow-sm transition transform hover:-translate-y-1"
                >
                    <div class="text-3xl mb-3">📅</div>
                    <div class="text-lg font-semibold text-green-700">
                        Laporan Harian
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        Kelola laporan kegiatan harian kamu.
                    </p>
                </a>

                <!-- Card: Info Akun -->
                <a
                    href="{{ route('student.info.edit') }}"
                    class="bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-xl p-5 shadow-sm transition transform hover:-translate-y-1"
                >
                    <div class="text-3xl mb-3">👤</div>
                    <div class="text-lg font-semibold text-purple-700">
                        Informasi Akun
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        Edit profil dan informasi akunmu.
                    </p>
                </a>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
            <div
                class="p-4 bg-green-100 text-green-800 border border-green-300 rounded-md text-sm"
            >
                ✅ {{ session("success") }}
            </div>
            @endif

            <hr class="mt-10 border-gray-200" />
        </div>
    </div>
</div>

@endsection
