<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i class="fas fa-user-circle text-purple-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Profil Saya</h2>
                    <p class="text-sm text-gray-600">Kelola informasi profil dan keamanan akun Anda</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Sidebar Profile Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                        <div class="text-center">
                            <div class="relative inline-block">
                                <img class="h-32 w-32 rounded-full object-cover border-4 border-purple-100"
                                    src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}"
                                    id="sidebar-photo">
                                <div
                                    class="absolute bottom-0 right-0 bg-purple-600 rounded-full p-2 border-4 border-white">
                                    <i class="fas fa-camera text-white text-sm"></i>
                                </div>
                            </div>
                            <h3 class="mt-4 text-xl font-bold text-gray-900">{{ auth()->user()->name }}</h3>
                            <p class="text-sm text-gray-600">{{ auth()->user()->email }}</p>
                            <div class="mt-3">
                                @php
                                    $roleConfig = match (auth()->user()->role) {
                                        'admin' => ['color' => 'purple', 'icon' => 'shield-alt'],
                                        'guru', 'dosen' => ['color' => 'blue', 'icon' => 'chalkboard-teacher'],
                                        'siswa', 'mahasiswa' => ['color' => 'green', 'icon' => 'user-graduate'],
                                        default => ['color' => 'gray', 'icon' => 'user'],
                                    };
                                @endphp
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-{{ $roleConfig['color'] }}-100 text-{{ $roleConfig['color'] }}-700 rounded-full text-xs font-medium">
                                    <i class="fas fa-{{ $roleConfig['icon'] }}"></i>
                                    {{ auth()->user()->role_display }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <a href="{{ route('notifications.preferences') }}"
                                class="flex items-center gap-3 w-full px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors mb-4">
                                <i class="fas fa-bell text-blue-600"></i>
                                <span class="text-sm font-medium">Pengaturan Notifikasi</span>
                                <i class="fas fa-chevron-right ml-auto text-blue-400 text-xs"></i>
                            </a>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Status Email</span>
                                    @if (auth()->user()->hasVerifiedEmail())
                                        <span class="inline-flex items-center gap-1 text-green-600 font-medium">
                                            <i class="fas fa-check-circle"></i>
                                            Terverifikasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-yellow-600 font-medium">
                                            <i class="fas fa-exclamation-circle"></i>
                                            Belum Terverifikasi
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Bergabung</span>
                                    <span
                                        class="text-gray-900 font-medium">{{ auth()->user()->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Profile Photo Section -->
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                        @include('profile.partials.update-profile-photo-form')
                    </div>

                    <!-- Profile Information Section -->
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <!-- Update Password Section -->
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                        @include('profile.partials.update-password-form')
                    </div>

                    <!-- Delete Account Section -->
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Profile Update Success
            @if (session('status') === 'profile-updated')
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Informasi profil Anda berhasil diperbarui.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            // Password Update Success
            @if (session('status') === 'password-updated')
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Password Anda berhasil diubah.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            // Email Verification Link Sent
            @if (session('status') === 'verification-link-sent')
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Link verifikasi baru telah dikirim ke alamat email Anda.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
            @endif

            // Show account deletion errors
            @if ($errors->userDeletion->any())
                @php
                    $deletionErrors = [];
                    foreach ($errors->userDeletion->all() as $error) {
                        $deletionErrors[] = $error;
                    }
                    $deletionErrorText = implode('<br>', $deletionErrors);
                @endphp
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menghapus Akun!',
                    html: '{!! $deletionErrorText !!}',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#EF4444'
                });
            @endif
        </script>
    @endpush
</x-app-layout>
