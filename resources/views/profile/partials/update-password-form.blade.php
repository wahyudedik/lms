<section>
    <header class="border-b border-gray-200 pb-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-green-100 rounded-lg">
                <i class="fas fa-lock text-green-600"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Perbarui Password</h2>
                <p class="text-sm text-gray-600">Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap
                    aman</p>
            </div>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-key text-gray-400 mr-1"></i>
                Password Saat Ini
            </label>
            <input id="update_password_current_password" name="current_password" type="password"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-colors"
                autocomplete="current-password" />
            @error('current_password', 'updatePassword')
                <p class="mt-1 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-lock text-gray-400 mr-1"></i>
                Password Baru
            </label>
            <input id="update_password_password" name="password" type="password"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-colors"
                autocomplete="new-password" />
            @error('password', 'updatePassword')
                <p class="mt-1 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">
                <i class="fas fa-info-circle"></i>
                Minimal 8 karakter, kombinasi huruf besar, huruf kecil, dan angka
            </p>
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-check-circle text-gray-400 mr-1"></i>
                Konfirmasi Password Baru
            </label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-colors"
                autocomplete="new-password" />
            @error('password_confirmation', 'updatePassword')
                <p class="mt-1 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white rounded-lg shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                <i class="fas fa-save"></i>
                <span>Simpan Password</span>
            </button>
        </div>
    </form>
</section>
