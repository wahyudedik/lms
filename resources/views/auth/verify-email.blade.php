<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-8">
        <div
            class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg mb-4">
            <i class="fas fa-envelope-open text-white text-2xl"></i>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Verifikasi Email Anda</h2>
        <p class="text-gray-600">Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda dengan
            mengklik link yang baru saja kami kirimkan.</p>
    </div>

    <!-- Success Message -->
    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-start gap-3">
                <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                <p class="text-sm font-medium text-green-800">
                    Link verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.
                </p>
            </div>
        </div>
    @endif

    <div class="space-y-4">
        <!-- Resend Button -->
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium">
                <i class="fas fa-paper-plane"></i>
                <span>Kirim Ulang Email Verifikasi</span>
            </button>
        </form>

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full text-center text-sm text-gray-600 hover:text-gray-900 font-medium py-2 transition-colors">
                <i class="fas fa-sign-out-alt mr-1"></i>
                Keluar
            </button>
        </form>
    </div>

    <!-- Info Box -->
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
            <div class="text-sm text-blue-800">
                <p class="font-medium mb-1">Tidak menerima email?</p>
                <p>Periksa folder spam Anda atau klik tombol di atas untuk mengirim ulang email verifikasi.</p>
            </div>
        </div>
    </div>
</x-guest-layout>
