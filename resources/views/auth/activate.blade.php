@extends('layouts.guest')
@section('title', 'Aktivasi Akun')

@section('content')
    <form method="POST" action="{{ route('activation.activate') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <p class="mb-4 text-sm text-gray-600">Selamat datang! Lengkapi data untuk mengaktifkan akun email: <strong>{{ $email }}</strong></p>

        <div>
            <label for="name">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
        </div>

        <div class="mt-4">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}" required>
        </div>

        <div class="mt-4">
            <label for="password">Password Baru</label>
            <input id="password" type="password" name="password" required>
        </div>

        <div class="mt-4">
            <label for="password_confirmation">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit">Aktifkan Akun</button>
        </div>
    </form>
@endsection