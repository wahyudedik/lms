<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('admin.documentation.index') }}"
                        class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="text-2xl font-bold text-gray-800">
                        <i class="fas {{ $doc['icon'] }} mr-2 text-indigo-600"></i>{{ $doc['title'] }}
                    </h2>
                </div>
                <p class="text-sm text-gray-600 ml-10">
                    <i class="fas fa-folder mr-1"></i>{{ $doc['category'] }}
                    <span class="mx-2">•</span>
                    <i class="fas fa-file mr-1"></i>{{ $doc['filename'] }}
                    <span class="mx-2">•</span>
                    <i class="fas fa-clock mr-1"></i>Updated
                    {{ \Carbon\Carbon::createFromTimestamp($doc['modified'])->diffForHumans() }}
                </p>
            </div>

            <div class="flex gap-2">
                <!-- Print Button -->
                <button onclick="window.print()"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                    <i class="fas fa-print mr-2"></i>Print
                </button>

                <!-- Download Button -->
                <a href="{{ asset('docs/' . $doc['filename']) }}" download="{{ $doc['filename'] }}"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-download mr-2"></i>Download
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex gap-6">
                <!-- Sidebar Navigation -->
                <div class="hidden lg:block w-64 flex-shrink-0">
                    <div class="bg-white rounded-lg shadow-md p-4 sticky top-6">
                        <h3 class="font-bold text-gray-800 mb-4">
                            <i class="fas fa-list mr-2 text-indigo-600"></i>Documentation
                        </h3>
                        <div class="space-y-1 text-sm">
                            @foreach ($docs as $d)
                                <a href="{{ route('admin.documentation.show', $d['slug']) }}"
                                    class="block px-3 py-2 rounded-lg transition-colors {{ $d['slug'] === $doc['slug'] ? 'bg-indigo-100 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
                                    <i class="fas {{ $d['icon'] }} mr-2 text-xs"></i>
                                    {{ Str::limit($d['title'], 30) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <!-- Document Content -->
                        <article class="prose prose-indigo max-w-none p-8 documentation-content">
                            {!! $html !!}
                        </article>
                    </div>

                    <!-- Navigation Footer -->
                    <div class="flex items-center justify-between mt-6">
                        @php
                            $currentIndex = array_search($doc['slug'], array_column($docs, 'slug'));
                            $prevDoc = $currentIndex > 0 ? $docs[$currentIndex - 1] : null;
                            $nextDoc = $currentIndex < count($docs) - 1 ? $docs[$currentIndex + 1] : null;
                        @endphp

                        @if ($prevDoc)
                            <a href="{{ route('admin.documentation.show', $prevDoc['slug']) }}"
                                class="flex items-center gap-3 px-6 py-3 bg-white rounded-lg shadow hover:shadow-md transition-shadow group">
                                <i
                                    class="fas fa-arrow-left text-gray-400 group-hover:text-indigo-600 transition-colors"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Previous</p>
                                    <p class="font-medium text-gray-800">{{ Str::limit($prevDoc['title'], 30) }}</p>
                                </div>
                            </a>
                        @else
                            <div></div>
                        @endif

                        @if ($nextDoc)
                            <a href="{{ route('admin.documentation.show', $nextDoc['slug']) }}"
                                class="flex items-center gap-3 px-6 py-3 bg-white rounded-lg shadow hover:shadow-md transition-shadow group">
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Next</p>
                                    <p class="font-medium text-gray-800">{{ Str::limit($nextDoc['title'], 30) }}</p>
                                </div>
                                <i
                                    class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-600 transition-colors"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('styles')
        <style>
            /* Documentation Content Styles */
            .documentation-content {
                font-size: 16px;
                line-height: 1.7;
            }

            .documentation-content h1 {
                font-size: 2.5rem;
                font-weight: 800;
                color: #1f2937;
                margin-top: 0;
                margin-bottom: 1.5rem;
                border-bottom: 3px solid #6366f1;
                padding-bottom: 0.5rem;
            }

            .documentation-content h2 {
                font-size: 2rem;
                font-weight: 700;
                color: #374151;
                margin-top: 2rem;
                margin-bottom: 1rem;
                border-bottom: 2px solid #e5e7eb;
                padding-bottom: 0.5rem;
            }

            .documentation-content h3 {
                font-size: 1.5rem;
                font-weight: 600;
                color: #4b5563;
                margin-top: 1.5rem;
                margin-bottom: 0.75rem;
            }

            .documentation-content h4 {
                font-size: 1.25rem;
                font-weight: 600;
                color: #6b7280;
                margin-top: 1.25rem;
                margin-bottom: 0.5rem;
            }

            .documentation-content p {
                margin-bottom: 1rem;
                color: #374151;
            }

            .documentation-content ul,
            .documentation-content ol {
                margin-bottom: 1rem;
                padding-left: 1.5rem;
            }

            .documentation-content li {
                margin-bottom: 0.5rem;
                color: #374151;
            }

            .documentation-content code {
                background-color: #f3f4f6;
                padding: 0.125rem 0.375rem;
                border-radius: 0.25rem;
                font-family: 'Monaco', 'Menlo', monospace;
                font-size: 0.875rem;
                color: #ec4899;
            }

            .documentation-content pre {
                background-color: #1f2937;
                color: #f9fafb;
                padding: 1rem;
                border-radius: 0.5rem;
                overflow-x: auto;
                margin-bottom: 1rem;
            }

            .documentation-content pre code {
                background-color: transparent;
                padding: 0;
                color: #f9fafb;
                font-size: 0.875rem;
            }

            .documentation-content a {
                color: #6366f1;
                text-decoration: underline;
            }

            .documentation-content a:hover {
                color: #4f46e5;
            }

            .documentation-content blockquote {
                border-left: 4px solid #6366f1;
                padding-left: 1rem;
                margin-left: 0;
                margin-bottom: 1rem;
                color: #6b7280;
                font-style: italic;
            }

            .documentation-content table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 1rem;
            }

            .documentation-content th,
            .documentation-content td {
                border: 1px solid #e5e7eb;
                padding: 0.5rem;
                text-align: left;
            }

            .documentation-content th {
                background-color: #f9fafb;
                font-weight: 600;
            }

            .documentation-content img {
                max-width: 100%;
                height: auto;
                border-radius: 0.5rem;
                margin-bottom: 1rem;
            }

            .documentation-content hr {
                border: none;
                border-top: 2px solid #e5e7eb;
                margin: 2rem 0;
            }

            /* Print Styles */
            @media print {
                .documentation-content {
                    font-size: 12pt;
                }

                .documentation-content pre {
                    background-color: #f3f4f6 !important;
                    color: #1f2937 !important;
                    border: 1px solid #d1d5db;
                }

                .documentation-content pre code {
                    color: #1f2937 !important;
                }
            }
        </style>
    @endpush
</x-app-layout>
