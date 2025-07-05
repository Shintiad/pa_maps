<x-guest-layout>
    <h4 class="pb-4 font-bold text-3xl font-mali">Register</h4>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" class="dark:text-gray-900" />
            <x-text-input id="name" class="block mt-1 w-full bg-white dark:bg-white text-gray-900 dark:text-gray-900 border-gray-300 dark:border-gray-300 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500" type="text" name="name" :value="old('name')" placeholder="John Smith" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" class="dark:text-gray-900" />
            <x-text-input id="email" class="block mt-1 w-full bg-white dark:bg-white text-gray-900 dark:text-gray-900 border-gray-300 dark:border-gray-300 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500" type="email" name="email" :value="old('email')" placeholder="smith@gmail.com" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone')" class="dark:text-gray-900" />
            <x-text-input id="phone" class="block mt-1 w-full bg-white dark:bg-white text-gray-900 dark:text-gray-900 border-gray-300 dark:border-gray-300 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500" type="text" name="phone" :value="old('phone')" placeholder="08**********" required autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="dark:text-gray-900" />

            <x-text-input id="password" class="block mt-1 w-full bg-white dark:bg-white text-gray-900 dark:text-gray-900 border-gray-300 dark:border-gray-300 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500"
                type="password"
                name="password"
                placeholder="********"
                required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="dark:text-gray-900" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full bg-white dark:bg-white text-gray-900 dark:text-gray-900 border-gray-300 dark:border-gray-300 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500"
                type="password"
                name="password_confirmation" 
                placeholder="********"
                required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-500 hover:!text-gray-900 dark:!hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4 !bg-gray-900 dark:!bg-gray-900 !text-white dark:!text-white !border !border-gray-900 dark:!border-gray-900 hover:!bg-gray-50 hover:!text-gray-900 dark:hover:!bg-gray-50 dark:hover:!text-gray-900 focus:!ring-indigo-500 dark:focus:!ring-indigo-500">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>