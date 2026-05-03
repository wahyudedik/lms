<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-wifi-slash mr-2"></i>{{ __('Offline Exams') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ __('Download exams for offline access in computer labs') }}</p>
            </div>

            <!-- Online Status Indicator -->
            <div id="onlineStatus" class="flex items-center gap-2 px-4 py-2.5 rounded-lg border border-gray-300 bg-white shadow-sm">
                <span id="statusDot" class="w-2 h-2 rounded-full bg-green-500"></span>
                <span id="statusText" class="font-semibold text-sm">{{ __('Online') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Info Banner -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">{{ __('Offline Mode') }}</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>{{ __('Download exams to take them offline in computer labs without stable internet connection.') }}
                            </p>
                            <ul class="list-disc list-inside mt-2 space-y-1">
                                <li>{{ __('Exams are cached in your browser') }}</li>
                                <li>{{ __('Answers saved locally and synced when online') }}</li>
                                <li>{{ __('Perfect for CBT in labs with unstable connection') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PWA Install Banner -->
            <div id="installBanner"
                class="bg-purple-600 rounded-lg p-6 mb-6 text-white shadow-md"
                style="display: none;">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="bg-purple-700 rounded-lg p-3">
                            <i class="fas fa-download text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">{{ __('Install LMS App') }}</h3>
                            <p class="text-sm text-purple-100">{{ __('Install for better offline experience and quick access') }}</p>
                        </div>
                    </div>
                    <button id="installButton"
                        class="inline-flex items-center gap-2 bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-purple-50 transition-all duration-200 shadow-sm">
                        <i class="fas fa-download"></i>
                        <span>{{ __('Install Now') }}</span>
                    </button>
                </div>
            </div>

            <!-- Storage Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-green-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg mr-3">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-green-600 text-xs font-semibold mb-1">{{ __('Cached Exams') }}</div>
                            <div class="text-2xl font-bold text-green-900" id="cachedExamsCount">0</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-blue-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg mr-3">
                            <i class="fas fa-database text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-blue-600 text-xs font-semibold mb-1">{{ __('Storage Used') }}</div>
                            <div class="text-2xl font-bold text-blue-900" id="storageUsed">0 MB</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-yellow-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-lg mr-3">
                            <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-yellow-600 text-xs font-semibold mb-1">{{ __('Pending Sync') }}</div>
                            <div class="text-2xl font-bold text-yellow-900" id="pendingSync">0</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exams List -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-list text-purple-600 mr-2"></i>{{ __('Available Offline Exams') }}
                    </h3>
                </div>

                <div class="divide-y divide-gray-200">
                    @forelse($exams as $exam)
                        <div class="p-6 hover:bg-gray-50 transition-colors" data-exam-id="{{ $exam->id }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h4 class="text-lg font-bold text-gray-800">{{ $exam->title }}</h4>
                                        <span data-exam-status="{{ $exam->id }}" class="text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                                <i class="fas fa-cloud mr-1"></i>{{ __('Online Only') }}
                                            </span>
                                        </span>
                                    </div>

                                    <p class="text-sm text-gray-600 mb-3">{{ $exam->description }}</p>

                                    <div class="flex items-center gap-6 text-sm text-gray-600">
                                        <span>
                                            <i class="fas fa-book-open mr-1 text-purple-500"></i>
                                            {{ $exam->course->title }}
                                        </span>
                                        <span>
                                            <i class="fas fa-question-circle mr-1 text-purple-500"></i>
                                            {{ $exam->questions->count() }} {{ __('Questions') }}
                                        </span>
                                        <span>
                                            <i class="fas fa-clock mr-1 text-purple-500"></i>
                                            {{ $exam->duration }} {{ __('minutes') }}
                                        </span>
                                        @if ($exam->last_attempt)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>
                                                {{ __('Attempted') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex gap-2 ml-4">
                                    <!-- Cache Button -->
                                    <button data-cache-exam="{{ $exam->id }}"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                        <i class="fas fa-download"></i>
                                        <span>{{ __('Cache for Offline') }}</span>
                                    </button>

                                    <!-- Take Exam Button -->
                                    <a href="{{ route('offline.exams.take', $exam) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                        <i class="fas fa-play"></i>
                                        <span>{{ __('Take Exam') }}</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Cache Progress Bar (hidden by default) -->
                            <div class="mt-3 hidden" id="cacheProgress{{ $exam->id }}">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-purple-600 h-full rounded-full transition-all" style="width: 0%"
                                            id="cacheBar{{ $exam->id }}"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700" id="cachePercent{{ $exam->id }}">0%</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                            </div>
                            <p class="text-gray-900 font-semibold mb-2">{{ __('No offline exams available') }}</p>
                            <p class="text-sm text-gray-500">{{ __('Contact your instructor to enable offline mode for exams') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex gap-3">
                <button id="clearCacheButton" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-red-300 text-red-600 font-semibold rounded-lg hover:bg-red-50 hover:border-red-400 transition-all duration-200 shadow-sm">
                    <i class="fas fa-trash"></i>
                    <span>{{ __('Clear All Cache') }}</span>
                </button>

                <button id="syncNowButton" onclick="offlineManager.syncQueuedSubmissions()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                    <i class="fas fa-sync"></i>
                    <span>{{ __('Sync Now') }}</span>
                </button>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/offline.js') }}"></script>
        <script>
            // Check cached status on load
            document.addEventListener('DOMContentLoaded', async () => {
                await checkCachedExams();
                await updateStorageInfo();
                await updatePendingSync();

                // Show install banner if available
                if (window.offlineManager.deferredPrompt) {
                    document.getElementById('installBanner').style.display = 'block';
                }
            });

            /**
             * Check which exams are cached
             */
            async function checkCachedExams() {
                try {
                    const db = await openIndexedDB();
                    const transaction = db.transaction(['exams'], 'readonly');
                    const store = transaction.objectStore('exams');
                    const allExams = await new Promise((resolve, reject) => {
                        const req = store.getAll();
                        req.onsuccess = () => resolve(req.result || []);
                        req.onerror = () => reject(req.error);
                    });

                    let cachedCount = 0;

                    allExams.forEach(examData => {
                        const examId = examData.exam.id;
                        window.offlineManager.updateCacheStatus(examId, true);
                        cachedCount++;
                    });

                    document.getElementById('cachedExamsCount').textContent = cachedCount;
                } catch (error) {
                    console.error('Failed to check cached exams:', error);
                }
            }

            /**
             * Update storage info
             */
            async function updateStorageInfo() {
                if ('storage' in navigator && 'estimate' in navigator.storage) {
                    const estimate = await navigator.storage.estimate();
                    const usedMB = (estimate.usage / 1024 / 1024).toFixed(2);
                    document.getElementById('storageUsed').textContent = usedMB + ' MB';
                }
            }

            /**
             * Update pending sync count
             */
            async function updatePendingSync() {
                try {
                    const response = await fetch('/offline/sync/status');
                    const data = await response.json();
                    document.getElementById('pendingSync').textContent = data.pending_submissions;
                } catch (error) {
                    console.error('Failed to get sync status:', error);
                }
            }

            /**
             * Open IndexedDB
             */
            function openIndexedDB() {
                return new Promise((resolve, reject) => {
                    const request = indexedDB.open('LMS_OfflineDB', 1);
                    request.onerror = () => reject(request.error);
                    request.onsuccess = () => resolve(request.result);
                });
            }

            // Refresh stats every 30 seconds
            setInterval(() => {
                updateStorageInfo();
                updatePendingSync();
            }, 30000);
        </script>
    @endpush
</x-app-layout>
