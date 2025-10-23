@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-database text-green-600 mr-2"></i>Backup Database
                </h1>
                <p class="text-gray-600 mt-1">Kelola backup database untuk keamanan data</p>
            </div>
            <a href="{{ route('admin.settings.index') }}"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <!-- Create Backup Button -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Buat Backup Baru</h2>
                    <p class="text-sm text-gray-600 mt-1">Backup akan disimpan di storage/app/backups</p>
                </div>
                <form action="{{ route('admin.settings.backup.create') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-plus-circle mr-2"></i>Buat Backup Sekarang
                    </button>
                </form>
            </div>
        </div>

        <!-- Backup List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-list text-blue-600 mr-2"></i>Daftar Backup ({{ $backups->count() }})
                </h2>
            </div>

            @if ($backups->count() > 0)
                <div class="divide-y">
                    @foreach ($backups as $backup)
                        <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition">
                            <div class="flex items-center flex-1">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-file-archive text-green-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">{{ $backup['filename'] }}</h3>
                                    <div class="flex items-center gap-4 mt-1 text-sm text-gray-600">
                                        <span><i class="fas fa-hdd mr-1"></i>{{ $backup['size'] }}</span>
                                        <span><i class="fas fa-clock mr-1"></i>{{ $backup['date'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.settings.backup.download', $backup['filename']) }}"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                                <form action="{{ route('admin.settings.backup.delete', $backup['filename']) }}"
                                    method="POST"
                                    onsubmit="return confirmDelete('Yakin ingin menghapus backup ini? File backup akan dihapus permanen!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                                        <i class="fas fa-trash mr-1"></i>Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center text-gray-500">
                    <i class="fas fa-inbox text-6xl mb-4"></i>
                    <h3 class="text-lg font-semibold mb-2">Belum Ada Backup</h3>
                    <p class="text-sm">Klik "Buat Backup Sekarang" untuk membuat backup pertama</p>
                </div>
            @endif
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border-l-4 border-blue-600 p-6 mt-6">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-600 text-xl mr-4"></i>
                <div>
                    <h3 class="font-semibold text-gray-800 mb-2">Informasi Penting</h3>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Backup dilakukan secara manual, disarankan backup rutin setiap minggu</li>
                        <li>• File backup berisi seluruh data database dalam format SQL</li>
                        <li>• Simpan file backup di tempat aman (eksternal drive, cloud storage)</li>
                        <li>• Untuk restore, hubungi administrator sistem</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
