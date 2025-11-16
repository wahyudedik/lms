<x-guest-layout>
    <div class="text-center mb-8">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-4">
            <i class="fas fa-envelope-open text-blue-600 text-2xl"></i>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ __('Verify Your Email') }}</h2>
        <p class="text-gray-600">{{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?') }}</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                <p class="text-sm font-medium text-green-800">
                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                </p>
            </div>
        </div>
    @endif

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="w-full justify-center py-3 text-base font-semibold">
                <i class="fas fa-paper-plane mr-2"></i>{{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-center text-sm text-gray-600 hover:text-gray-900 font-medium py-2 transition-colors">
                <i class="fas fa-sign-out-alt mr-2"></i>{{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
