/**
 * Laravel LMS Service Worker
 * Provides offline support for CBT exams in local lab environments
 */

const CACHE_VERSION = 'lms-v1.0.0';
const CACHE_NAME = `lms-cache-${CACHE_VERSION}`;

// Assets to cache immediately on install
const PRECACHE_ASSETS = [
    '/',
    '/css/app.css',
    '/js/app.js',
    '/manifest.json',
    '/offline.html',
];

// Cache strategies
const CACHE_FIRST = ['css', 'js', 'fonts', 'images'];
const NETWORK_FIRST = ['api', 'exams'];

/**
 * Install Event - Precache assets
 */
self.addEventListener('install', (event) => {
    console.log('[Service Worker] Installing...');

    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[Service Worker] Precaching assets');
                return cache.addAll(PRECACHE_ASSETS);
            })
            .then(() => self.skipWaiting())
    );
});

/**
 * Activate Event - Clean up old caches
 */
self.addEventListener('activate', (event) => {
    console.log('[Service Worker] Activating...');

    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames
                        .filter((cacheName) => cacheName !== CACHE_NAME)
                        .map((cacheName) => {
                            console.log('[Service Worker] Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        })
                );
            })
            .then(() => self.clients.claim())
    );
});

/**
 * Fetch Event - Handle requests with appropriate strategy
 */
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') {
        // Handle POST requests for exam submission
        if (url.pathname.includes('/exams/') && request.method === 'POST') {
            event.respondWith(handleExamSubmission(request));
        }
        return;
    }

    // Offline exam pages - always cache
    if (url.pathname.includes('/offline/exams/')) {
        event.respondWith(cacheFirstStrategy(request));
        return;
    }

    // API requests - network first, fallback to cache
    if (url.pathname.includes('/api/')) {
        event.respondWith(networkFirstStrategy(request));
        return;
    }

    // Static assets - cache first
    if (isCacheFirstRequest(url.pathname)) {
        event.respondWith(cacheFirstStrategy(request));
        return;
    }

    // Default - network first
    event.respondWith(networkFirstStrategy(request));
});

/**
 * Cache First Strategy
 * Try cache first, fallback to network
 */
async function cacheFirstStrategy(request) {
    try {
        const cache = await caches.open(CACHE_NAME);
        const cachedResponse = await cache.match(request);

        if (cachedResponse) {
            console.log('[Service Worker] Cache hit:', request.url);
            return cachedResponse;
        }

        console.log('[Service Worker] Cache miss, fetching:', request.url);
        const networkResponse = await fetch(request);

        // Cache the response for future use
        if (networkResponse.ok) {
            cache.put(request, networkResponse.clone());
        }

        return networkResponse;
    } catch (error) {
        console.error('[Service Worker] Cache first failed:', error);
        return caches.match('/offline.html');
    }
}

/**
 * Network First Strategy
 * Try network first, fallback to cache
 */
async function networkFirstStrategy(request) {
    try {
        const networkResponse = await fetch(request);

        // Cache successful responses
        if (networkResponse.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }

        return networkResponse;
    } catch (error) {
        console.log('[Service Worker] Network failed, trying cache:', request.url);

        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }

        // Return offline page for navigation requests
        if (request.mode === 'navigate') {
            return caches.match('/offline.html');
        }

        throw error;
    }
}

/**
 * Handle Exam Submission
 * Queue submissions when offline
 */
async function handleExamSubmission(request) {
    try {
        // Try to submit online
        const response = await fetch(request);
        return response;
    } catch (error) {
        console.log('[Service Worker] Offline - queueing exam submission');

        // Queue for later submission
        const requestData = await request.clone().json();
        await queueSubmission({
            url: request.url,
            method: request.method,
            data: requestData,
            timestamp: Date.now()
        });

        // Return success response
        return new Response(JSON.stringify({
            success: true,
            message: 'Submission queued for sync',
            offline: true
        }), {
            status: 202,
            headers: { 'Content-Type': 'application/json' }
        });
    }
}

/**
 * Queue submission for later sync
 */
async function queueSubmission(submission) {
    const db = await openDatabase();
    const transaction = db.transaction(['submissions'], 'readwrite');
    const store = transaction.objectStore('submissions');

    await store.add(submission);

    console.log('[Service Worker] Submission queued:', submission);
}

/**
 * Open IndexedDB
 */
function openDatabase() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('LMS_OfflineDB', 1);

        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve(request.result);

        request.onupgradeneeded = (event) => {
            const db = event.target.result;

            // Create object stores
            if (!db.objectStoreNames.contains('submissions')) {
                const store = db.createObjectStore('submissions', {
                    keyPath: 'id',
                    autoIncrement: true
                });
                store.createIndex('timestamp', 'timestamp', { unique: false });
            }

            if (!db.objectStoreNames.contains('exams')) {
                db.createObjectStore('exams', { keyPath: 'id' });
            }
        };
    });
}

/**
 * Check if request should use cache-first strategy
 */
function isCacheFirstRequest(pathname) {
    return CACHE_FIRST.some(type => pathname.includes(`/${type}/`));
}

/**
 * Background Sync Event
 * Sync queued submissions when back online
 */
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-submissions') {
        console.log('[Service Worker] Background sync triggered');
        event.waitUntil(syncQueuedSubmissions());
    }
});

/**
 * Sync Queued Submissions
 */
async function syncQueuedSubmissions() {
    try {
        const db = await openDatabase();
        const transaction = db.transaction(['submissions'], 'readonly');
        const store = transaction.objectStore('submissions');
        const submissions = await store.getAll();

        console.log(`[Service Worker] Syncing ${submissions.length} submissions`);

        for (const submission of submissions) {
            try {
                const response = await fetch(submission.url, {
                    method: submission.method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(submission.data)
                });

                if (response.ok) {
                    // Remove from queue
                    const deleteTransaction = db.transaction(['submissions'], 'readwrite');
                    const deleteStore = deleteTransaction.objectStore('submissions');
                    await deleteStore.delete(submission.id);

                    console.log('[Service Worker] Submission synced:', submission.id);
                }
            } catch (error) {
                console.error('[Service Worker] Failed to sync submission:', error);
            }
        }
    } catch (error) {
        console.error('[Service Worker] Sync failed:', error);
    }
}

/**
 * Message Event
 * Handle messages from clients
 */
self.addEventListener('message', (event) => {
    if (event.data.action === 'skipWaiting') {
        self.skipWaiting();
    }

    if (event.data.action === 'clearCache') {
        event.waitUntil(
            caches.keys()
                .then((cacheNames) => {
                    return Promise.all(
                        cacheNames.map((cacheName) => caches.delete(cacheName))
                    );
                })
        );
    }

    if (event.data.action === 'cacheExam') {
        event.waitUntil(cacheExamData(event.data.examId));
    }
});

/**
 * Cache exam data for offline access
 */
async function cacheExamData(examId) {
    try {
        const cache = await caches.open(CACHE_NAME);

        // Cache exam page
        await cache.add(`/offline/exams/${examId}`);

        // Cache exam data API
        await cache.add(`/api/offline/exams/${examId}`);

        console.log('[Service Worker] Exam cached:', examId);
    } catch (error) {
        console.error('[Service Worker] Failed to cache exam:', error);
    }
}

console.log('[Service Worker] Loaded successfully');
