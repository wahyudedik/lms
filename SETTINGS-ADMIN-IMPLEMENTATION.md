# ⚙️ Settings & Admin Panel - Complete Implementation Guide

**Status:** ✅ 95% Complete (Needs Views Creation)  
**Date:** October 22, 2025

---

## 📦 What's Already Implemented

### **1. Database** ✅
- ✅ `settings` table migration (key-value pairs)
- ✅ Seeded with default settings (app name, logo, colors, etc.)

### **2. Backend** ✅
- ✅ `Setting` model with helper methods (`get()`, `set()`, `getAllGrouped()`)
- ✅ `SettingsController` with:
  - Settings management (view, update)
  - Logo/favicon upload
  - **Database backup** (create, download, delete)
  - Backup file listing with size and date
- ✅ Caching support for performance

### **3. Default Settings** ✅
```
General:
- app_name: Laravel LMS
- app_description: Platform Pembelajaran Online
- school_name, school_address, school_phone, school_email

Appearance:
- primary_color: #3B82F6 (blue)
- secondary_color: #10B981 (green)
- app_logo, app_favicon

System:
- enable_registration: true
- enable_email_verification: true
- maintenance_mode: false
- max_upload_size: 50MB

Notification:
- notification_enabled: true
- email_notifications: false
```

---

## ⏳ What Needs to Be Done

### **STEP 1: Add Routes** 🔧

**File:** `routes/web.php`

Add inside the `admin` middleware group:

```php
// Settings & Backup (admin only)
Route::get('settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
Route::post('settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');

// Database Backup
Route::get('settings/backup', [App\Http\Controllers\Admin\SettingsController::class, 'backup'])->name('settings.backup');
Route::post('settings/backup/create', [App\Http\Controllers\Admin\SettingsController::class, 'createBackup'])->name('settings.backup.create');
Route::get('settings/backup/{filename}/download', [App\Http\Controllers\Admin\SettingsController::class, 'downloadBackup'])->name('settings.backup.download');
Route::delete('settings/backup/{filename}', [App\Http\Controllers\Admin\SettingsController::class, 'deleteBackup'])->name('settings.backup.delete');
```

**Location:** Add after exam/question routes, before closing `});`

---

### **STEP 2: Create Settings Views** 🎨

#### **A. Settings Index** (`resources/views/admin/settings/index.blade.php`)

```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-cog text-blue-600 mr-2"></i>Pengaturan Sistem
            </h1>
            <p class="text-gray-600 mt-1">Kelola pengaturan aplikasi dan sekolah</p>
        </div>
        <a href="{{ route('admin.settings.backup') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-database mr-2"></i>Backup Database
        </a>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- General Settings -->
        @if(isset($settings['general']))
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>Pengaturan Umum
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($settings['general'] as $setting)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                            </label>
                            @if($setting->type === 'textarea')
                                <textarea name="settings[{{ $setting->key }}]" rows="3" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $setting->value }}</textarea>
                            @else
                                <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Appearance Settings -->
        @if(isset($settings['appearance']))
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                    <i class="fas fa-palette text-purple-600 mr-2"></i>Tampilan
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($settings['appearance'] as $setting)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                            </label>
                            
                            @if($setting->type === 'color')
                                <input type="color" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" 
                                    class="w-full h-12 px-2 border border-gray-300 rounded-lg cursor-pointer">
                            
                            @elseif($setting->type === 'file')
                                @if($setting->value)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($setting->value) }}" alt="Current" class="h-20 rounded-lg border">
                                    </div>
                                @endif
                                <input type="file" name="settings[{{ $setting->key }}]" accept="image/*"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG (Max 2MB)</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- System Settings -->
        @if(isset($settings['system']))
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                    <i class="fas fa-server text-red-600 mr-2"></i>Pengaturan Sistem
                </h2>
                <div class="space-y-4">
                    @foreach($settings['system'] as $setting)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label class="text-sm font-medium text-gray-700">
                                    {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                </label>
                                @if($setting->type === 'number')
                                    <p class="text-xs text-gray-500">Ukuran dalam bytes (50MB = 52428800)</p>
                                @endif
                            </div>
                            
                            @if($setting->type === 'boolean')
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="settings[{{ $setting->key }}]" value="1" 
                                        {{ $setting->value == '1' ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            @elseif($setting->type === 'number')
                                <input type="number" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" 
                                    class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Notification Settings -->
        @if(isset($settings['notification']))
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                    <i class="fas fa-bell text-yellow-600 mr-2"></i>Notifikasi
                </h2>
                <div class="space-y-4">
                    @foreach($settings['notification'] as $setting)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <label class="text-sm font-medium text-gray-700">
                                {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                            </label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="settings[{{ $setting->key }}]" value="1" 
                                    {{ $setting->value == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Submit Button -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.dashboard') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
```

---

#### **B. Backup Page** (`resources/views/admin/settings/backup.blade.php`)

```blade
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
        <a href="{{ route('admin.settings.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
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
                <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
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

        @if($backups->count() > 0)
            <div class="divide-y">
                @foreach($backups as $backup)
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
                            <form action="{{ route('admin.settings.backup.delete', $backup['filename']) }}" method="POST" 
                                  onsubmit="return confirmDelete()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
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
```

---

### **STEP 3: Add Navigation Link** 🔗

**File:** `resources/views/layouts/navigation.blade.php`

Add in Admin navigation section:

```blade
<x-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')">
    {{ __('Settings') }}
</x-nav-link>
```

---

## 🧪 Testing Guide

### **Test Settings Management**

```
1. Login as Admin
2. Navigate to /admin/settings
3. Update app name, school info
4. Upload logo
5. Change colors
6. Toggle system settings
7. Click "Simpan Pengaturan"
8. Verify changes are saved
9. Refresh page → changes should persist
```

### **Test Database Backup**

```
1. Login as Admin
2. Navigate to /admin/settings/backup
3. Click "Buat Backup Sekarang"
4. Wait for backup to complete
5. Verify backup file appears in list
6. Click "Download" → file should download
7. Click "Hapus" → confirm deletion → file removed
```

---

## 📊 Summary

### **Implemented:**
- ✅ Settings table (key-value pairs)
- ✅ Setting model with caching
- ✅ SettingsController (CRUD + Backup)
- ✅ Default settings seeded
- ✅ Logo/favicon upload
- ✅ Database backup (create, download, delete)
- ✅ Backup file management

### **Ready to Use:**
Once you create the 2 views and add routes, Module 7 is **100% complete**!

**Time Estimate:** 10-15 minutes (copy-paste views above)

---

## 🎉 Module 7 Complete!

After implementing the views and routes above, you'll have:
- ✅ Comprehensive settings management
- ✅ Logo and theme customization
- ✅ Database backup system
- ✅ Professional admin panel

**Total Implementation:** ~1,200 lines of code

Ready for **PRODUCTION**! 🚀

