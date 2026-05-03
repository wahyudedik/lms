<section>
    <header class="border-b border-gray-200 pb-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-red-100 rounded-lg">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Hapus Akun</h2>
                <p class="text-sm text-gray-600">Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus
                    secara permanen</p>
            </div>
        </div>
    </header>

    <div class="mt-6">
        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-sm text-red-800 font-medium">Peringatan: Tindakan ini tidak dapat dibatalkan!</p>
                    <p class="text-sm text-red-700 mt-1">
                        Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen.
                        Sebelum menghapus akun, silakan unduh data atau informasi yang ingin Anda simpan.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <button type="button" id="delete-account-btn"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 text-white rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                <i class="fas fa-trash-alt"></i>
                <span>Hapus Akun</span>
            </button>
        </div>
    </div>

    <form id="delete-account-form" method="post" action="{{ route('profile.destroy') }}" style="display: none;">
        @csrf
        @method('delete')
        <input type="password" name="password" id="delete-password-input" required />
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteBtn = document.getElementById('delete-account-btn');
            const deleteForm = document.getElementById('delete-account-form');
            const passwordInput = document.getElementById('delete-password-input');

            deleteBtn.addEventListener('click', async function() {
                const {
                    value: password
                } = await Swal.fire({
                    title: 'Hapus Akun?',
                    html: `
                        <div class="text-left space-y-4">
                            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm text-red-800 font-medium mb-2">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Peringatan: Tindakan ini tidak dapat dibatalkan!
                                </p>
                                <p class="text-sm text-red-700">
                                    Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen.
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-key mr-1"></i>
                                    Masukkan password Anda untuk konfirmasi:
                                </label>
                                <input type="password"
                                       id="swal-password"
                                       class="swal2-input w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-200"
                                       placeholder="Password Anda"
                                       autocomplete="current-password">
                            </div>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Ya, Hapus Akun Saya',
                    cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
                    focusConfirm: false,
                    customClass: {
                        popup: 'swal-wide',
                        htmlContainer: 'text-left'
                    },
                    preConfirm: () => {
                        const password = document.getElementById('swal-password').value;
                        if (!password) {
                            Swal.showValidationMessage(
                                '<i class="fas fa-exclamation-circle mr-1"></i> Silakan masukkan password Anda'
                                );
                            return false;
                        }
                        return password;
                    },
                    didOpen: () => {
                        document.getElementById('swal-password').focus();
                    }
                });

                if (password) {
                    passwordInput.value = password;
                    deleteForm.submit();
                }
            });
        });
    </script>

    <style>
        .swal-wide {
            width: 600px !important;
            max-width: 90% !important;
        }
    </style>
</section>
