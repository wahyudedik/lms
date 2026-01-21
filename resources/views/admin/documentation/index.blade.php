<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-book mr-2 text-indigo-600"></i>📚 {{ __('System Documentation') }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">
            {{ __('Complete guides and documentation for optimizing the Laravel LMS application') }}</p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-indigo-100 rounded-full p-3">
                            <i class="fas fa-file-alt text-indigo-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Total Docs') }}</p>
                            <p class="text-2xl font-bold text-gray-800">{{ count($docs) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-certificate text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Certificates') }}</p>
                            <p class="text-2xl font-bold text-gray-800">
                                {{ collect($docs)->where('category', 'Certificates')->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-purple-100 rounded-full p-3">
                            <i class="fas fa-wifi-slash text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Offline Mode') }}</p>
                            <p class="text-2xl font-bold text-gray-800">
                                {{ collect($docs)->where('category', 'Offline Mode')->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-yellow-100 rounded-full p-3">
                            <i class="fas fa-star text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Categories') }}</p>
                            <p class="text-2xl font-bold text-gray-800">
                                {{ collect($docs)->pluck('category')->unique()->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documentation by Category -->
            @php
                $groupedDocs = collect($docs)->groupBy('category');
            @endphp

            @foreach ($groupedDocs as $category => $categoryDocs)
                <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fas {{ $categoryDocs->first()['icon'] }}"></i>
                            {{ $category }}
                        </h3>
                        <p class="text-indigo-100 text-sm mt-1">
                            {{ trans_choice(':count document available|:count documents available', $categoryDocs->count(), ['count' => $categoryDocs->count()]) }}
                        </p>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @foreach ($categoryDocs as $doc)
                            <a href="{{ route('admin.documentation.show', $doc['slug']) }}"
                                class="block p-6 hover:bg-gray-50 transition-colors group">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div
                                                class="bg-indigo-100 rounded-lg p-2 group-hover:bg-indigo-200 transition-colors">
                                                <i class="fas {{ $doc['icon'] }} text-indigo-600"></i>
                                            </div>
                                            <div>
                                                <h4
                                                    class="text-lg font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">
                                                    {{ $doc['title'] }}
                                                </h4>
                                                <p class="text-xs text-gray-500">
                                                    <i class="fas fa-file mr-1"></i>{{ $doc['filename'] }}
                                                </p>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-600 ml-12">
                                            {{ $doc['description'] }}
                                        </p>
                                        <div class="flex items-center gap-4 mt-3 ml-12 text-xs text-gray-500">
                                            <span>
                                                <i class="fas fa-hdd mr-1"></i>
                                                {{ number_format($doc['size'] / 1024, 1) }} {{ __('KB') }}
                                            </span>
                                            <span>
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ __('Updated :time', ['time' => \Carbon\Carbon::createFromTimestamp($doc['modified'])->diffForHumans()]) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div
                                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium text-sm group-hover:bg-indigo-700 transition-colors">
                                            <i class="fas fa-arrow-right mr-2"></i>{{ __('Read') }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Quick Links -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-indigo-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-rocket mr-2 text-indigo-600"></i>{{ __('Quick Access') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('admin.certificate-settings.index') }}"
                        class="flex items-center gap-3 p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                        <i class="fas fa-certificate text-green-600 text-2xl"></i>
                        <div>
                            <p class="font-medium text-gray-800">{{ __('Certificate Settings') }}</p>
                            <p class="text-xs text-gray-500">{{ __('Configure templates') }}</p>
                        </div>
                    </a>

                    <a href="{{ route('offline.exams.index') }}"
                        class="flex items-center gap-3 p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                        <i class="fas fa-wifi-slash text-purple-600 text-2xl"></i>
                        <div>
                            <p class="font-medium text-gray-800">{{ __('Offline Exams') }}</p>
                            <p class="text-xs text-gray-500">{{ __('Test offline mode') }}</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.settings.index') }}"
                        class="flex items-center gap-3 p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                        <i class="fas fa-cog text-blue-600 text-2xl"></i>
                        <div>
                            <p class="font-medium text-gray-800">{{ __('System Settings') }}</p>
                            <p class="text-xs text-gray-500">{{ __('General configuration') }}</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="mt-6 bg-white rounded-lg shadow p-6">
                <div class="flex items-start gap-4">
                    <div class="bg-indigo-100 rounded-full p-3 flex-shrink-0">
                        <i class="fas fa-layer-group text-indigo-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 mb-2">{{ __('UI Style Guide') }}</h4>
                        <p class="text-sm text-gray-600 mb-4">
                            {{ __('Panduan ini memastikan konsistensi layout, grid, typography, dan komponen di seluruh halaman.') }}
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="border border-gray-100 rounded-lg p-4">
                                <p class="text-xs font-semibold uppercase text-gray-500 mb-2">{{ __('Layout') }}</p>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>{{ __('Gunakan page-shell untuk container utama (max-w-7xl, padding responsif).') }}
                                    </li>
                                    <li>{{ __('Gunakan page-section untuk jarak antar section (space-y-6).') }}</li>
                                    <li>{{ __('Gunakan page-grid untuk grid responsif (1/2/3 kolom).') }}</li>
                                </ul>
                            </div>
                            <div class="border border-gray-100 rounded-lg p-4">
                                <p class="text-xs font-semibold uppercase text-gray-500 mb-2">{{ __('Typography') }}
                                </p>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>{{ __('H1: text-2xl sm:text-3xl, bold, gray-900.') }}</li>
                                    <li>{{ __('H2: text-xl sm:text-2xl, semibold, gray-900.') }}</li>
                                    <li>{{ __('Paragraf: text-sm, gray-600.') }}</li>
                                </ul>
                            </div>
                            <div class="border border-gray-100 rounded-lg p-4">
                                <p class="text-xs font-semibold uppercase text-gray-500 mb-2">{{ __('Komponen') }}</p>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>{{ __('Card: gunakan class card, card-header, card-body.') }}</li>
                                    <li>{{ __('Tabel: gunakan class table-base, table-th, table-td.') }}</li>
                                    <li>{{ __('Button: gunakan btn-primary, btn-secondary, btn-muted, btn-danger.') }}
                                    </li>
                                </ul>
                            </div>
                            <div class="border border-gray-100 rounded-lg p-4">
                                <p class="text-xs font-semibold uppercase text-gray-500 mb-2">{{ __('Responsif') }}</p>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>{{ __('Gunakan grid responsif dan utilitas sm/md/lg untuk layout.') }}</li>
                                    <li>{{ __('Hindari fixed width; gunakan max-w dan flex wrapping.') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Info -->
            <div class="mt-6 bg-white rounded-lg shadow p-6">
                <div class="flex items-start gap-4">
                    <div class="bg-blue-100 rounded-full p-3 flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 mb-2">{{ __('About Documentation') }}</h4>
                        <p class="text-sm text-gray-600 mb-2">
                            {{ __('This documentation provides comprehensive guides for all major features and systems in the Laravel LMS application.') }}
                        </p>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li><i
                                    class="fas fa-check text-green-600 mr-2"></i>{{ __('Complete system guides and tutorials') }}
                            </li>
                            <li><i
                                    class="fas fa-check text-green-600 mr-2"></i>{{ __('Configuration and customization instructions') }}
                            </li>
                            <li><i
                                    class="fas fa-check text-green-600 mr-2"></i>{{ __('Troubleshooting and best practices') }}
                            </li>
                            <li><i
                                    class="fas fa-check text-green-600 mr-2"></i>{{ __('API references and technical details') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
