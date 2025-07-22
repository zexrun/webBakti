@extends('layouts.guest')
@section('title', 'Login')

@section('content')
    @if (@session('status'))
        <div class="">
            {{ session('status') }}
        </div>        
    @endif

    @if ($errors->any())
        <div class="">
            <div class="">{{ __('Whoops! Something went wrong') }}</div>
            <ul class="">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>        
    @endif


    <form method="post" action="{{ route('login') }}" class="w-full h-full flex flex-col items-center justify-center p-5 gap-3">
        @csrf

        <div class="w-full">
            <input id="login" type="text" name="login" :value="old('login')" class="input-form" placeholder="Username or Email" required autofocus />
        </div>

        <div class="w-full">
            <input id="password" type="password" name="password" class="input-form" placeholder="Enter Password" required autocomplete="current-password" />
        </div>

        <div class="w-full flex flex-row justify-between">
            <label for="remember_me" class="flex flex-row items-center justify-start gap-2">
                <input id="remember_me" type="checkbox" name="remember" class="w-[10px] h-[10px] border-gray-500 focus:outline-none">
                <span class="text-[10px] text-gray-500">Keep me logged in</span>
            </label>
            <a href="{{ route('password.request')}}" class="flex flex-row items-center justify-start text-gray-500 text-[10px] gap-2">Forgot Password</a>
        </div>

        <div class="w-full flex items-center justify-center">
            <button type="submit" class="btn-primary">
                Log In
            </button>
        </div>
    </form>
@endsection