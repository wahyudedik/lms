<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-8">
        <div
            class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl shadow-lg mb-4">
            <i class="fas fa-shield-alt text-white text-2xl"></i>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Konfirmasi Password</h2>
        <p class="text-gray-600">Ini adalah area aman. Mohon konfirmasi password Anda sebelum melanjutkan.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
        @csrf

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-lock text-gray-400 mr-1"></i>
                Password
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    placeholder="••••••••"
                    class="block w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-colors" />
            </div>
            @error('password')
                <p class="mt-1 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Confirm Button -->
        <div>
            <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-yellow-600 text-white rounded-lg shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors font-medium">
                <i class="fas fa-check"></i>
                <span>Konfirmasi</span>
            </button>
        </div>
    </form>

    <!-- Info Box -->
    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-yellow-600 mt-0.5"></i>
            <div class="text-sm text-yellow-800">
                <p class="font-medium">Mengapa saya perlu mengkonfirmasi password?</p>
                <p class="mt-1">Untuk keamanan akun Anda, kami memerlukan konfirmasi password sebelum mengakses area
                    sensitif.</p>
            </div>
        </div>
    </div>
</x-guest-layout>
