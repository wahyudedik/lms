<x-guest-layout>
    <div class="text-center mb-8">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-4">
            <i class="fas fa-key text-blue-600 text-2xl"></i>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Forgot Password?</h2>
        <p class="text-gray-600">No problem. Just let us know your email address and we will email you a password reset link.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400"></i>
                </div>
                <x-text-input id="email" class="block w-full pl-10" type="email" name="email" :value="old('email')" required autofocus placeholder="you@example.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-primary-button class="w-full justify-center py-3 text-base font-semibold">
                <i class="fas fa-paper-plane mr-2"></i>{{ __('Send Reset Link') }}
            </x-primary-button>
        </div>

        <div class="text-center text-sm text-gray-600">
            <a class="text-blue-600 hover:text-blue-800 font-medium transition-colors" href="{{ route('login') }}">
                <i class="fas fa-arrow-left mr-1"></i>{{ __('Back to login') }}
            </a>
        </div>
    </form>
</x-guest-layout>
