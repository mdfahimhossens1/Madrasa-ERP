<x-guest-layout>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Mobile Number -->
        <div>
            <x-input-label for="phone" :value="__('মোবাইল নম্বর')" />

            <x-text-input
                id="phone"
                class="block mt-1 w-full"
                type="text"
                name="phone"
                :value="old('phone')"
                required
                autofocus
                autocomplete="username"
                placeholder="01XXXXXXXXX" />

            <x-input-error
                :messages="$errors->get('phone')"
                class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('পাসওয়ার্ড')" />

            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password" />

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">

                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">

                <span class="ms-2 text-sm text-gray-600">
                    আমাকে মনে রাখুন
                </span>

            </label>
        </div>

        <div class="flex items-center justify-between mt-4">

            @if (Route::has('password.request'))
                <a
                    class="underline text-sm text-gray-600 hover:text-gray-900"
                    href="{{ route('password.request') }}">
                    পাসওয়ার্ড ভুলে গেছেন?
                </a>
            @endif

            <x-primary-button>
                লগইন করুন
            </x-primary-button>

        </div>

    </form>

</x-guest-layout>