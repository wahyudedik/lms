<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    🔌 Offline Exams
                </h2>
                <p class="text-sm text-gray-600 mt-1">Download exams for offline access in computer labs</p>
            </div>

            <!-- Online Status Indicator -->
            <div id="onlineStatus" class="flex items-center gap-2 px-4 py-2 rounded-lg border">
                <span id="statusDot">🟢</span>
                <span id="statusText" class="font-medium">Online</span>
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
                        <h3 class="text-sm font-medium text-blue-800">Offline Mode</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Download exams to take them offline in computer labs without stable internet connection.
                            </p>
                            <ul class="list-disc list-inside mt-2 space-y-1">
                                <li>Exams are cached in your browser</li>
                                <li>Answers saved locally and synced when online</li>
                                <li>Perfect for CBT in labs with unstable connection</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PWA Install Banner -->
            <div id="installBanner"
                class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg p-6 mb-6 text-white"
                style="display: none;">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/20 rounded-full p-3">
                            <i class="fas fa-download text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Install LMS App</h3>
                            <p class="text-sm opacity-90">Install for better offline experience and quick access</p>
                        </div>
                    </div>
                    <button id="installButton"
                        class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-opacity-90 transition-all">
                        <i class="fas fa-download mr-2"></i>Install Now
                    </button>
                </div>
            </div>

            <!-- Storage Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Cached Exams</p>
                            <p class="text-2xl font-bold text-gray-800" id="cachedExamsCount">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-100 rounded-full p-3">
                            <i class="fas fa-database text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Storage Used</p>
                            <p class="text-2xl font-bold text-gray-800" id="storageUsed">0 MB</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-yellow-100 rounded-full p-3">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Pending Sync</p>
                            <p class="text-2xl font-bold text-gray-800" id="pendingSync">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exams List -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-list mr-2 text-indigo-600"></i>Available Offline Exams
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
                                            <span class="text-gray-400">
                                                <i class="fas fa-cloud mr-1"></i>Online Only
                                            </span>
                                        </span>
                                    </div>

                                    <p class="text-sm text-gray-600 mb-3">{{ $exam->description }}</p>

                                    <div class="flex items-center gap-6 text-sm text-gray-600">
                                        <span>
                                            <i class="fas fa-book-open mr-1 text-indigo-500"></i>
                                            {{ $exam->course->title }}
                                        </span>
                                        <span>
                                            <i class="fas fa-question-circle mr-1 text-indigo-500"></i>
                                            {{ $exam->questions->count() }} Questions
                                        </span>
                                        <span>
                                            <i class="fas fa-clock mr-1 text-indigo-500"></i>
                                            {{ $exam->duration }} minutes
                                        </span>
                                        @if ($exam->last_attempt)
                                            <span class="text-green-600 font-medium">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Attempted
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex gap-2 ml-4">
                                    <!-- Cache Button -->
                                    <button data-cache-exam="{{ $exam->id }}"
                                        class="btn btn-primary px-4 py-2 rounded-lg font-medium transition-all">
                                        <i class="fas fa-download mr-2"></i>Cache for Offline
                                    </button>

                                    <!-- Take Exam Button -->
                                    <a href="{{ route('offline.exams.take', $exam) }}"
                                        class="btn btn-outline px-4 py-2 rounded-lg font-medium transition-all">
                                        <i class="fas fa-play mr-2"></i>Take Exam
                                    </a>
                                </div>
                            </div>

                            <!-- Cache Progress Bar (hidden by default) -->
                            <div class="mt-3 hidden" id="cacheProgress{{ $exam->id }}">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-indigo-600 h-full rounded-full transition-all" style="width: 0%"
                                            id="cacheBar{{ $exam->id }}"></div>
                                    </div>
                                    <span class="text-sm text-gray-600" id="cachePercent{{ $exam->id }}">0%</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-600 mb-2">No offline exams available</p>
                            <p class="text-sm text-gray-500">Contact your instructor to enable offline mode for exams
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex gap-3">
                <button id="clearCacheButton" class="btn btn-outline text-red-600 border-red-600 hover:bg-red-50">
                    <i class="fas fa-trash mr-2"></i>Clear All Cache
                </button>

                <button id="syncNowButton" class="btn btn-outline" onclick="offlineManager.syncQueuedSubmissions()">
                    <i class="fas fa-sync mr-2"></i>Sync Now
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
                    const allExams = await store.getAll();

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
