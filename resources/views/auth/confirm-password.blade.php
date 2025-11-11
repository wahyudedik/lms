<x-guest-layout>
    <div class="text-center mb-8">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
            <i class="fas fa-shield-alt text-yellow-600 text-2xl"></i>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Confirm Password</h2>
        <p class="text-gray-600">This is a secure area. Please confirm your password before continuing.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <x-text-input id="password" class="block w-full pl-10" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-primary-button class="w-full justify-center py-3 text-base font-semibold">
                <i class="fas fa-check mr-2"></i>{{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
