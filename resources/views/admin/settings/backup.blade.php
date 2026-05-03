@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-database text-green-600 mr-2"></i>{{ __('Backup Database') }}
                </h1>
                <p class="text-gray-600 mt-1">{{ __('Manage database backups for data security') }}</p>
            </div>
            <a href="{{ route('admin.settings.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                <i class="fas fa-arrow-left"></i>
                <span>{{ __('Back') }}</span>
            </a>
        </div>

        <!-- Create Backup Button -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">{{ __('Create New Backup') }}</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ __('Backups are stored in storage/app/backups') }}</p>
                </div>
                <form action="{{ route('admin.settings.backup.create') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <i class="fas fa-plus-circle"></i>
                        <span>{{ __('Create Backup Now') }}</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Backup List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-list text-blue-600 mr-2"></i>{{ __('Backup List (:count)', ['count' => $backups->count()]) }}
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
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md text-sm">
                                    <i class="fas fa-download"></i>
                                    <span>{{ __('Download') }}</span>
                                </a>
                                <form action="{{ route('admin.settings.backup.delete', $backup['filename']) }}"
                                    method="POST"
                                    onsubmit="return confirmDelete('{{ __('Are you sure you want to delete this backup? This action cannot be undone!') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all duration-200 shadow-sm hover:shadow-md text-sm">
                                        <i class="fas fa-trash"></i>
                                        <span>{{ __('Delete') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center text-gray-500">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('No Backups Yet') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('Click "Create Backup Now" to create your first backup') }}</p>
                </div>
            @endif
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border-l-4 border-blue-600 p-6 mt-6">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-600 text-xl mr-4"></i>
                <div>
                    <h3 class="font-semibold text-gray-800 mb-2">{{ __('Important Information') }}</h3>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• {{ __('Backups are manual; we recommend weekly backups') }}</li>
                        <li>• {{ __('Backup files contain the entire database in SQL format') }}</li>
                        <li>• {{ __('Store backup files securely (external drive, cloud storage)') }}</li>
                        <li>• {{ __('For restore operations, contact the system administrator') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
