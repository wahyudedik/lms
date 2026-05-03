<section>
    <header class="border-b border-gray-200 pb-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-100 rounded-lg">
                <i class="fas fa-user-edit text-blue-600"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Informasi Profil</h2>
                <p class="text-sm text-gray-600">Perbarui informasi profil dan alamat email akun Anda</p>
            </div>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-user text-gray-400 mr-1"></i>
                Nama Lengkap
            </label>
            <input id="name" name="name" type="text"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors"
                value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            @error('name')
                <p class="mt-1 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-envelope text-gray-400 mr-1"></i>
                Alamat Email
            </label>
            <input id="email" name="email" type="email"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors"
                value="{{ old('email', $user->email) }}" required autocomplete="username" />
            @error('email')
                <p class="mt-1 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div class="mt-3 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="text-sm text-yellow-800 font-medium">Email Belum Terverifikasi</p>
                            <p class="text-sm text-yellow-700 mt-1">
                                Alamat email Anda belum diverifikasi.
                                <button form="send-verification"
                                    class="font-medium text-yellow-900 underline hover:text-yellow-800 focus:outline-none focus:ring-2 focus:ring-yellow-500 rounded">
                                    Klik di sini untuk mengirim ulang email verifikasi
                                </button>
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                <i class="fas fa-save"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</section>
