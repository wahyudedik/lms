# 🔌 Offline Mode for Local Lab CBT

Complete guide untuk Offline Mode - memungkinkan siswa mengerjakan ujian CBT tanpa koneksi internet yang stabil.

## 📋 Overview

Offline Mode adalah Progressive Web App (PWA) feature yang memungkinkan:
- ✅ Download exam untuk offline access
- ✅ Take exams tanpa internet connection
- ✅ Auto-save answers secara lokal
- ✅ Auto-sync ketika kembali online
- ✅ Perfect untuk lab komputer dengan koneksi unstable

## 🎯 Use Cases

### 1. Computer Lab dengan Internet Tidak Stabil
- Students download exam sebelum ujian dimulai
- Take exam offline tanpa worry tentang koneksi
- Answers saved locally dan sync ketika online

### 2. CBT di Daerah Remote
- Download multiple exams ahead of time
- Students dapat take exams meski internet down
- Submission queue automatically sync when available

### 3. Mobile/Portable CBT
- Install app di laptop/tablet
- Take exams di mana saja offline
- Perfect untuk field tests

## 🚀 Quick Start

### For Students

**1. Access Offline Exams**
```
Dashboard → Offline Exams
```

**2. Install App (Optional but Recommended)**
- Click "Install App" button saat banner muncul
- App akan available di desktop/home screen
- Faster access dan better offline experience

**3. Download Exam**
- Click "Cache for Offline" button pada exam
- Wait for caching progress complete
- Exam ready untuk offline access!

**4. Take Exam Offline**
- Click "Take Exam" button
- Exam akan load meski offline
- Answers saved automatically locally
- Akan sync when back online

### For Admin/Instructors

**1. Enable Offline Mode untuk Exam**
```
Admin → Exams → Edit Exam → Offline Mode Settings
```

**2. Configure Settings**
- ☑️ Enable Offline Mode
- ⏱️ Set Cache Duration (1-168 hours)
- 💾 Save changes

**3. Publish Exam**
- Students sekarang bisa download exam
- Monitor offline attempts via dashboard

## 📱 PWA Installation

### Desktop (Chrome/Edge)
1. Visit LMS website
2. Look for "Install" icon di address bar
3. Click install
4. App appears di desktop

### Mobile (Android)
1. Visit LMS website
2. Tap menu (3 dots)
3. Select "Add to Home Screen"
4. App appears di home screen

### Mobile (iOS)
1. Visit LMS di Safari
2. Tap share button
3. Select "Add to Home Screen"
4. App appears di home screen

## 🔧 Technical Details

### Service Worker

Service Worker handles:
- **Caching** - Static assets & exam data
- **Offline Fallback** - Serve cached content when offline
- **Background Sync** - Queue submissions for sync
- **Cache Management** - Auto-update cache

File: `public/service-worker.js`

### IndexedDB Storage

Local database stores:
- **Exams** - Full exam data (questions, options)
- **Submissions** - Queued answers waiting to sync
- **Metadata** - Cache timestamps, sync status

Storage managed by `public/js/offline.js`

### Caching Strategy

**Cache-First** (untuk static assets):
```
Request → Check Cache → Return if found → Fetch if not
```

**Network-First** (untuk dynamic content):
```
Request → Try Network → Fallback to Cache if offline
```

**Background Sync** (untuk submissions):
```
Submit → Queue if offline → Auto-sync when online
```

## ⚙️ Configuration

### Exam Settings

**Cache Duration**
- Default: 24 hours
- Range: 1-168 hours (1 week max)
- Configurable per exam

**Offline Features**
- Auto-save every 30 seconds
- Manual save on answer change
- Local storage backup
- Timer continues offline

### System Requirements

**Browser Support:**
- ✅ Chrome 80+
- ✅ Edge 80+
- ✅ Firefox 75+
- ✅ Safari 13.1+
- ✅ Mobile browsers (Android/iOS)

**Storage Requirements:**
- ~5-10 MB per exam
- IndexedDB support required
- Service Worker support required

**Recommended:**
- 50+ MB available storage
- Modern browser (last 2 years)
- JavaScript enabled

## 📊 Storage Management

### Check Storage Usage

```javascript
// Via browser console
navigator.storage.estimate().then(estimate => {
    const usage = (estimate.usage / 1024 / 1024).toFixed(2);
    const quota = (estimate.quota / 1024 / 1024).toFixed(2);
    console.log(`Using ${usage} MB of ${quota} MB`);
});
```

### Clear Cache

**Via UI:**
```
Offline Exams → Clear All Cache button
```

**Via Console:**
```javascript
// Clear service worker cache
caches.keys().then(names => {
    names.forEach(name => caches.delete(name));
});

// Clear IndexedDB
indexedDB.deleteDatabase('LMS_OfflineDB');
```

## 🔐 Security

### Data Protection

- ✅ Encrypted HTTPS connection
- ✅ Local storage isolated per user
- ✅ Service worker scope limited
- ✅ No sensitive data in cache
- ✅ Token-based authentication

### Anti-Cheat

Offline mode still enforces:
- ✅ Timer countdown
- ✅ Tab switch detection
- ✅ Fullscreen requirement
- ✅ Answer submission tracking
- ✅ Attempt limits

### Privacy

- Cached data only accessible by user
- Cleared when cache expires
- No data shared between devices
- Can be manually cleared anytime

## 🐛 Troubleshooting

### Exam Won't Cache

**Problem:** "Failed to cache exam" error

**Solutions:**
1. Check storage space
2. Try different browser
3. Clear existing cache
4. Disable extensions
5. Check console for errors

```bash
# Check browser console (F12)
# Look for service worker errors
```

### Answers Not Saving

**Problem:** Changes not saving offline

**Solutions:**
1. Check browser storage
2. Verify IndexedDB enabled
3. Check network status indicator
4. Try manual save
5. Check browser console

```javascript
// Check if IndexedDB working
indexedDB.open('test').onsuccess = () => console.log('IndexedDB OK');
```

### Submissions Not Syncing

**Problem:** Answers queued but not submitting

**Solutions:**
1. Check internet connection
2. Verify you're logged in
3. Check server status
4. Try manual sync button
5. Check submission queue

```javascript
// Check pending submissions
fetch('/offline/sync/status')
    .then(r => r.json())
    .then(d => console.log('Pending:', d.pending_submissions));
```

### Service Worker Not Registering

**Problem:** Offline mode not available

**Solutions:**
1. Must use HTTPS (or localhost)
2. Check browser support
3. Clear browser data
4. Try incognito mode
5. Check service worker status

```javascript
// Check service worker
navigator.serviceWorker.getRegistrations()
    .then(r => console.log('Registered:', r.length));
```

### App Not Installing

**Problem:** Install prompt doesn't appear

**Solutions:**
1. Check PWA criteria met
2. Clear browser data
3. Try different browser
4. Check manifest.json
5. Verify HTTPS connection

```bash
# Check PWA readiness
# Chrome DevTools → Application → Manifest
# Chrome DevTools → Application → Service Workers
```

## 📈 Monitoring

### Admin Dashboard

Monitor offline usage:
```
Admin → Analytics → Offline Stats
```

**Metrics:**
- Active offline sessions
- Cached exam count
- Pending submissions
- Sync success rate
- Storage usage trends

### Developer Tools

**Chrome DevTools:**
```
F12 → Application Tab
- Service Workers
- Cache Storage
- IndexedDB
- Storage Usage
```

**Network Throttling:**
```
F12 → Network → Throttling
- Test offline mode
- Simulate slow connection
- Verify offline fallback
```

## 🔄 Update & Maintenance

### Service Worker Updates

When code updates:
1. New service worker downloaded
2. Old worker continues running
3. User prompted to reload
4. New version activates on reload

**Force Update:**
```javascript
// Skip waiting and activate immediately
navigator.serviceWorker.controller.postMessage({ action: 'skipWaiting' });
```

### Cache Invalidation

Automatic:
- Service worker version change
- Cache max-age expired
- Manual clear cache

Manual:
```javascript
// Clear specific cache
caches.delete('lms-cache-v1.0.0');
```

## 📚 API Reference

### Offline Manager

```javascript
// Global instance
window.offlineManager

// Methods
offlineManager.installApp()              // Trigger install prompt
offlineManager.cacheExam(examId)        // Cache specific exam
offlineManager.clearCache()              // Clear all cache
offlineManager.syncQueuedSubmissions()  // Manual sync
offlineManager.updateCacheStatus(id, status)  // Update UI
```

### Service Worker Messages

```javascript
// Send message to service worker
navigator.serviceWorker.controller.postMessage({
    action: 'cacheExam',
    examId: 123
});

// Listen for messages
navigator.serviceWorker.addEventListener('message', event => {
    console.log('Message:', event.data);
});
```

### Storage APIs

```javascript
// IndexedDB
const db = await openIndexedDB();
const transaction = db.transaction(['exams'], 'readonly');
const store = transaction.objectStore('exams');
const exam = await store.get(examId);

// Cache API
const cache = await caches.open('lms-cache-v1.0.0');
await cache.add('/offline/exams/123');
const response = await cache.match('/offline/exams/123');
```

## 🎓 Best Practices

### For Administrators

1. **Enable offline mode early** - Give students time to download
2. **Set reasonable cache duration** - Balance freshness vs availability
3. **Test offline mode** - Before actual exam
4. **Monitor sync status** - Check for stuck submissions
5. **Educate students** - Provide clear instructions

### For Students

1. **Download before exam** - Don't wait until last minute
2. **Install PWA** - Better experience
3. **Test offline mode** - Make sure it works
4. **Keep browser updated** - For best compatibility
5. **Clear old cache** - Free up space

### For Developers

1. **Version service worker** - For cache invalidation
2. **Test offline** - Use DevTools throttling
3. **Handle errors gracefully** - Offline-first mindset
4. **Monitor storage** - Check quota
5. **Update docs** - Keep this guide current

## 🌟 Features

### ✅ Current Features

- PWA installation
- Service worker caching
- IndexedDB storage
- Offline exam taking
- Auto-save answers
- Background sync
- Queue management
- Online/offline status
- Storage monitoring
- Cache management

### 🚧 Planned Features

- Offline analytics
- Bulk exam download
- Offline messaging
- P2P sync
- Advanced caching strategies
- Cache compression
- Offline search
- Conflict resolution

## 📞 Support

**Issues?**
- Check troubleshooting section
- Review browser console
- Contact system administrator
- Submit bug report

**Feature Requests?**
- Suggest improvements
- Vote on roadmap
- Contribute code

---

**Offline Mode - Take Exams Anywhere, Anytime! 🚀📱**

Perfect untuk CBT di lab komputer dengan koneksi internet tidak stabil!

