/**
 * Offline Functionality for Laravel LMS
 * Handles PWA installation, service worker, and offline exam caching
 */

class OfflineManager {
    constructor() {
        this.serviceWorker = null;
        this.deferredPrompt = null;
        this.init();
    }

    /**
     * Initialize offline manager
     */
    async init() {
        this.checkSupport();
        this.registerServiceWorker();
        this.listenForInstallPrompt();
        this.updateOnlineStatus();
        this.setupEventListeners();
    }

    /**
     * Check browser support
     */
    checkSupport() {
        if (!('serviceWorker' in navigator)) {
            console.warn('Service Workers not supported in this browser');
            return false;
        }

        if (!('indexedDB' in window)) {
            console.warn('IndexedDB not supported in this browser');
            return false;
        }

        return true;
    }

    /**
     * Register service worker
     */
    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/service-worker.js', {
                    scope: '/'
                });

                this.serviceWorker = registration;

                console.log('[Offline] Service Worker registered:', registration);

                // Check for updates
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;

                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            this.notifyUpdate();
                        }
                    });
                });

                // Listen for messages from service worker
                navigator.serviceWorker.addEventListener('message', (event) => {
                    this.handleServiceWorkerMessage(event.data);
                });

            } catch (error) {
                console.error('[Offline] Service Worker registration failed:', error);
            }
        }
    }

    /**
     * Listen for install prompt
     */
    listenForInstallPrompt() {
        window.addEventListener('beforeinstallprompt', (event) => {
            // Prevent default prompt
            event.preventDefault();

            // Store event for later use
            this.deferredPrompt = event;

            // Show install button
            this.showInstallButton();

            console.log('[Offline] Install prompt ready');
        });

        // Listen for install success
        window.addEventListener('appinstalled', () => {
            console.log('[Offline] App installed successfully');
            this.hideInstallButton();
            this.showToast('App installed successfully!', 'success');
        });
    }

    /**
     * Show install button
     */
    showInstallButton() {
        const installButton = document.getElementById('installButton');
        if (installButton) {
            installButton.style.display = 'block';
        }
    }

    /**
     * Hide install button
     */
    hideInstallButton() {
        const installButton = document.getElementById('installButton');
        if (installButton) {
            installButton.style.display = 'none';
        }
    }

    /**
     * Trigger install prompt
     */
    async installApp() {
        if (!this.deferredPrompt) {
            console.warn('[Offline] Install prompt not available');
            return;
        }

        // Show prompt
        this.deferredPrompt.prompt();

        // Wait for user choice
        const choiceResult = await this.deferredPrompt.userChoice;

        if (choiceResult.outcome === 'accepted') {
            console.log('[Offline] User accepted install');
        } else {
            console.log('[Offline] User dismissed install');
        }

        // Clear prompt
        this.deferredPrompt = null;
        this.hideInstallButton();
    }

    /**
     * Update online status
     */
    updateOnlineStatus() {
        const updateStatus = () => {
            const statusElement = document.getElementById('onlineStatus');
            const statusDot = document.getElementById('statusDot');
            const statusText = document.getElementById('statusText');

            if (navigator.onLine) {
                if (statusElement) statusElement.classList.remove('offline');
                if (statusElement) statusElement.classList.add('online');
                if (statusDot) statusDot.innerHTML = '🟢';
                if (statusText) statusText.textContent = 'Online';

                // Try to sync queued submissions
                this.syncQueuedSubmissions();
            } else {
                if (statusElement) statusElement.classList.remove('online');
                if (statusElement) statusElement.classList.add('offline');
                if (statusDot) statusDot.innerHTML = '🔴';
                if (statusText) statusText.textContent = 'Offline';
            }
        };

        window.addEventListener('online', () => {
            updateStatus();
            this.showToast('Back online! Syncing...', 'success');
        });

        window.addEventListener('offline', () => {
            updateStatus();
            this.showToast('You are offline. Changes will be saved locally.', 'warning');
        });

        updateStatus();
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Install button
        const installButton = document.getElementById('installButton');
        if (installButton) {
            installButton.addEventListener('click', () => this.installApp());
        }

        // Cache exam buttons
        document.querySelectorAll('[data-cache-exam]').forEach(button => {
            button.addEventListener('click', (e) => {
                const examId = e.target.dataset.cacheExam;
                this.cacheExam(examId);
            });
        });

        // Clear cache button
        const clearCacheButton = document.getElementById('clearCacheButton');
        if (clearCacheButton) {
            clearCacheButton.addEventListener('click', () => this.clearCache());
        }
    }

    /**
     * Cache exam for offline access
     */
    async cacheExam(examId) {
        try {
            // Show loading
            this.showToast('Caching exam...', 'info');

            // Tell service worker to cache this exam
            if (navigator.serviceWorker.controller) {
                navigator.serviceWorker.controller.postMessage({
                    action: 'cacheExam',
                    examId: examId
                });
            }

            // Also fetch and store in IndexedDB
            const response = await fetch(`/offline/exams/${examId}/data`);
            const examData = await response.json();

            await this.saveToIndexedDB('exams', examData);

            this.showToast('Exam cached successfully!', 'success');

            // Update UI
            this.updateCacheStatus(examId, true);

        } catch (error) {
            console.error('[Offline] Failed to cache exam:', error);
            this.showToast('Failed to cache exam', 'error');
        }
    }

    /**
     * Save to IndexedDB
     */
    async saveToIndexedDB(storeName, data) {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open('LMS_OfflineDB', 1);

            request.onerror = () => reject(request.error);

            request.onsuccess = () => {
                const db = request.result;
                const transaction = db.transaction([storeName], 'readwrite');
                const store = transaction.objectStore(storeName);

                const putRequest = store.put(data);

                putRequest.onsuccess = () => resolve();
                putRequest.onerror = () => reject(putRequest.error);
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;

                if (!db.objectStoreNames.contains('exams')) {
                    db.createObjectStore('exams', { keyPath: 'exam.id' });
                }

                if (!db.objectStoreNames.contains('submissions')) {
                    const submissionStore = db.createObjectStore('submissions', {
                        keyPath: 'id',
                        autoIncrement: true
                    });
                    submissionStore.createIndex('timestamp', 'timestamp', { unique: false });
                }
            };
        });
    }

    /**
     * Update cache status in UI
     */
    updateCacheStatus(examId, isCached) {
        const cacheButtons = document.querySelectorAll(`[data-cache-exam="${examId}"]`);

        cacheButtons.forEach(button => {
            if (isCached) {
                button.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Cached';
                button.classList.remove('btn-primary');
                button.classList.add('btn-success');
                button.disabled = true;
            } else {
                button.innerHTML = '<i class="fas fa-download mr-2"></i>Cache for Offline';
                button.classList.remove('btn-success');
                button.classList.add('btn-primary');
                button.disabled = false;
            }
        });

        // Update cached status indicator
        const statusIndicator = document.querySelector(`[data-exam-status="${examId}"]`);
        if (statusIndicator) {
            statusIndicator.innerHTML = isCached
                ? '<span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Cached</span>'
                : '<span class="text-gray-400"><i class="fas fa-cloud mr-1"></i>Online Only</span>';
        }
    }

    /**
     * Clear cache
     */
    async clearCache() {
        if (confirm('Are you sure you want to clear all cached data?')) {
            try {
                // Clear service worker cache
                if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                    navigator.serviceWorker.controller.postMessage({ action: 'clearCache' });
                }

                // Clear IndexedDB
                await this.clearIndexedDB();

                this.showToast('Cache cleared successfully!', 'success');

                // Reload page
                setTimeout(() => window.location.reload(), 1000);

            } catch (error) {
                console.error('[Offline] Failed to clear cache:', error);
                this.showToast('Failed to clear cache', 'error');
            }
        }
    }

    /**
     * Clear IndexedDB
     */
    async clearIndexedDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.deleteDatabase('LMS_OfflineDB');
            request.onsuccess = () => resolve();
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Sync queued submissions
     */
    async syncQueuedSubmissions() {
        try {
            if (this._syncRequested) {
                return;
            }
            this._syncRequested = true;
            // Trigger background sync
            if ('serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
                const registration = await navigator.serviceWorker.ready;
                await registration.sync.register('sync-submissions');

                console.log('[Offline] Background sync registered');
            }
            // Clear the flag after a short delay to allow another manual request later
            setTimeout(() => {
                this._syncRequested = false;
            }, 5000);
        } catch (error) {
            console.error('[Offline] Failed to sync:', error);
            this._syncRequested = false;
        }
    }

    /**
     * Handle service worker message
     */
    handleServiceWorkerMessage(data) {
        console.log('[Offline] Message from service worker:', data);

        if (data.type === 'CACHE_UPDATED') {
            this.showToast('Content updated', 'info');
        }

        if (data.type === 'SYNC_COMPLETE') {
            this.showToast('Sync complete!', 'success');
        }
    }

    /**
     * Notify update available
     */
    notifyUpdate() {
        if (confirm('New version available! Reload to update?')) {
            window.location.reload();
        }
    }

    /**
     * Show toast notification
     */
    showToast(message, type = 'info') {
        // Use SweetAlert2 if available
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 3000
            });
        } else {
            console.log(`[Toast] ${type}: ${message}`);
        }
    }
}

// Initialize offline manager
const offlineManager = new OfflineManager();

// Export for global access
window.offlineManager = offlineManager;

