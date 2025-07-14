<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="login" :value="__('Login')" />
            <x-text-input id="login" class="block mt-1 w-full" type="text" name="login" :value="old('login')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>



<div class="flex flex-col w-full overflow-hidden relative min-h-screen radial-gradient items-center justify-center g-0 px-4">
    <div class="justify-center items-center w-full lg:flex max-w-md">
        <div class="w-full card-body">
            <form>
                <div class="mb-4">
                    <label for="login"
                           class="block text-sm mb-2 text-gray-400">Username/Email</label>
                    <input type="text" id="login"
                           class="py-3 px-4 block w-full border-gray-200 rounded-sm text-sm focus:border-blue-600 focus:ring-0" aria-describedby="hs-input-helper-text">
                </div>
                <div class="mb-6">
                    <label for="password"
                           class="block text-sm mb-2 text-gray-400">Password</label>
                    <input type="password" id="password"
                           class="py-3 px-4 block w-full border-gray-200 rounded-sm text-sm focus:border-blue-600 focus:ring-0" aria-describedby="hs-input-helper-text">
                </div>

                <div class="flex justify-between">
                    <div class="flex">
                        <input type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded-[4px] text-blue-600 focus:ring-blue-500" id="hs-default-checkbox" checked>
                        <label for="hs-default-checkbox" class="text-sm text-gray-500 ms-3">Remember this Device</label>
                    </div>
                        <a href="../" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Forgot Password?</a>
                </div>

                    <div class="grid my-6">
                        <a href="../" class="btn py-[10px] text-base text-white font-medium hover:bg-blue-700">Sign In</a>
                    </div>
            </form>
        </div>
    </div>
</div>
