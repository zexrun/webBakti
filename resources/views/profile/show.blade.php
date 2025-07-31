@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
<div class="base-div">
    <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
        <h2 class="text-3xl font-bold text-gray-800 dark:text-white text-center mb-6">Profil Pengguna</h2>

        <div class="space-y-2">
            {{-- Username --}}
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Username</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-200">{{ $user->username }}</p>
            </div>

            {{-- Nama Lengkap --}}
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Lengkap</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-200">{{ $user->name }}</p>
            </div>

            {{-- Email --}}
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Alamat Email</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-200">{{ $user->email }}</p>
            </div>

            {{-- Peran (Role) dengan kustomisasi --}}
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Peran</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    <span class="inline-block px-3 py-1 text-sm font-medium rounded-full
                        @if ($user->role == 'admin')
                            bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                        @elseif ($user->role == 'supervisor')
                            bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                        @elseif ($user->role == 'student')
                            bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @else
                            bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                        @endif
                    ">
                        @switch($user->role)
                            @case('supervisor')
                                Pembimbing
                                @break
                            @case('student')
                                Mahasiswa
                                @break
                            @case('admin')
                                Administrator
                                @break
                            @default
                                {{ ucfirst($user->role) }}
                        @endswitch
                    </span>
                </p>
            </div>
        </div>

        {{-- Contoh tombol kembali --}}
        <div class="mt-8 text-center">
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-700 dark:hover:bg-indigo-800">
                Kembali
            </a>
        </div>
    </div>
</div>
@endsection