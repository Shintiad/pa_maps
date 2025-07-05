<x-guest-layout>
    <h4 class="pb-4 font-bold text-3xl font-mali">Login</h4>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="dark:text-gray-900" />
            <x-text-input id="email" class="block mt-1 w-full bg-white dark:bg-white text-gray-900 dark:text-gray-900 border-gray-300 dark:border-gray-300 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500" type="email" name="email" :value="old('email')" placeholder="smith@gmail.com" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="dark:text-gray-900" />

            <x-text-input id="password" class="block mt-1 w-full bg-white dark:bg-white text-gray-900 dark:text-gray-900 border-gray-300 dark:border-gray-300 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500"
                type="password"
                name="password"
                placeholder="********"
                required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-white border-gray-300 dark:border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-500 focus:ring-offset-white dark:focus:ring-offset-white" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-2 mt-4">
            <!-- "Don't have an account?" link -->
            <a class="underline text-sm text-gray-500 dark:text-gray-500 hover:!text-gray-900 dark:!hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('register') }}">
                {{ __('Don\'t have an account?') }}
            </a>

            <!-- "Forgot your password?" link -->
            @if (Route::has('password.request'))
            <a class="underline text-sm text-gray-500 dark:text-gray-500 hover:!text-gray-900 dark:!hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                {{ __('Forgot your password?') }}
            </a>
            @endif

            <!-- Log in button -->
            <x-primary-button class="!bg-gray-900 dark:!bg-gray-900 !text-white dark:!text-white !border !border-gray-900 dark:!border-gray-900 hover:!bg-gray-50 hover:!text-gray-900 dark:hover:!bg-gray-50 dark:hover:!text-gray-900 focus:!ring-indigo-500 dark:focus:!ring-indigo-500">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

    </form>
</x-guest-layout>