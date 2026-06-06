<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Username/Email Field -->
        <div>
            <x-input-label for="login" :value="__('Username or Email')" />
            <x-text-input id="login" class="block mt-1 w-full" type="text" name="login" :value="old('login')" required autofocus autocomplete="username" placeholder="ইউজারনেম বা ইমেইল দিন" />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <!-- Madrasa Code Field (Optional) -->
        <div class="mt-4">
            <x-input-label for="madrasa_code" :value="__('Madrasa Code (Optional)')" />
            <x-text-input id="madrasa_code" class="block mt-1 w-full" type="text" name="madrasa_code" :value="old('madrasa_code')" placeholder="শুধুমাত্র নির্দিষ্ট মাদ্রাসার জন্য" />
            <x-input-error :messages="$errors->get('madrasa_code')" class="mt-2" />
            <p class="text-xs text-gray-500 mt-1">সুপার অ্যাডমিন বা সফট অ্যাডমিনের জন্য প্রয়োজন নেই</p>
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

        <!-- Demo Credentials (Development Only) -->
        @if(app()->environment('local'))
        <div class="mt-6 p-4 bg-gray-100 rounded-lg">
            <p class="text-sm font-semibold mb-2">ডেমো ক্রেডেনশিয়াল:</p>
            <div class="text-xs space-y-1">
                <p><strong>সুপার অ্যাডমিন:</strong> superadmin@gmail.com / fahim</p>
                <p><strong>মাদ্রাসা অ্যাডমিন:</strong> admin@alhuda.edu.bd / fahim (মাদ্রাসা কোড: ALHUDA001)</p>
                <p><strong>টিচার:</strong> teacher@alhuda.edu.bd / fahim (মাদ্রাসা কোড: ALHUDA001)</p>
                <p><strong>স্টুডেন্ট:</strong> student@alhuda.edu.bd / fahim (মাদ্রাসা কোড: ALHUDA001)</p>
            </div>
        </div>
        @endif
    </form>
</x-guest-layout>