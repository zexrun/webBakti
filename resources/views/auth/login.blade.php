@extends('layouts.guest')
@section('title', 'Login')

@section('content')
    {{-- Status Message --}}
    @if (session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('status') }}</span>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Whoops!</strong>
            <span class="block sm:inline">Something went wrong.</span>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Login Form --}}
    <form method="post" action="{{ route('login') }}" class="w-full flex flex-col gap-4">
        @csrf

        {{-- Username or Email Input --}}
        <div>
            <label for="login" class="block text-sm font-medium text-gray-700 mb-1">Username or Email</label>
            <input id="login" type="text" name="login" value="{{ old('login') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm"
                   placeholder="Enter your email or username" required autofocus />
        </div>

        {{-- Password Input --}}
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input id="password" type="password" name="password"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm"
                   placeholder="Enter your password" required autocomplete="current-password" />
        </div>

        {{-- Remember Me and Forgot Password --}}
        <div class="flex flex-col sm:flex-row justify-between items-center text-sm">
            <label for="remember_me" class="flex items-center space-x-2 cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember" class="text-blue-600 border-gray-300 rounded">
                <span class="text-gray-600">Keep me logged in</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request')}}" class="text-blue-600 hover:underline">Forgot Password?</a>
            @endif
        </div>

        {{-- Login Button --}}
        <div class="mt-4">
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg shadow-md transition-colors duration-300">
                Log In
            </button>
        </div>
    </form>
@endsection
