# 🔔 Notifications & Dashboard - Implementation Guide

**Status:** ⚠️ 80% Complete (Needs Controller Updates)  
**Date:** October 22, 2025

---

## ✅ What's Been Implemented

### **1. Backend Infrastructure** ✅
- ✅ **Notifications Table**: Migration created and run
- ✅ **Notification Classes**: 
  - `MaterialPublished` - Notifikasi materi baru
  - `ExamScheduled` - Notifikasi ujian tersedia
  - `ExamGraded` - Notifikasi nilai keluar
- ✅ **NotificationController**: CRUD operations for notifications
- ✅ **Routes**: 5 notification routes added

### **2. Frontend** ✅
- ✅ **Notification Bell Component**: Dropdown with live updates
- ✅ **Notifications Index Page**: Full list with pagination
- ✅ **Navigation Integration**: Bell icon added to top bar
- ✅ **Alpine.js Integration**: Real-time updates every 30 seconds

---

## ⏳ What Needs to Be Done

### **STEP 1: Trigger Notifications in Controllers**

You need to manually add notification triggers in the following controllers:

#### **A. Material Published (`MaterialController`)**

**File:** `app/Http/Controllers/Guru/MaterialController.php` (or Admin)

**Add to `store()` method after material is saved:**

```php
use App\Notifications\MaterialPublished;

public function store(Request $request, Course $course)
{
    // ... existing validation and save logic ...
    
    $material = $course->materials()->create($validated);
    
    // 🆕 TRIGGER NOTIFICATION: Send to all enrolled students
    $enrolledStudents = $course->enrollments()
        ->where('status', 'active')
        ->with('user')
        ->get()
        ->pluck('user');
    
    foreach ($enrolledStudents as $student) {
        $student->notify(new MaterialPublished($material));
    }
    
    return redirect()->route('guru.courses.materials.index', $course)
        ->with('success', 'Materi berhasil ditambahkan dan notifikasi dikirim ke siswa');
}
```

**Also add to `update()` method if material status changes to 'published':**

```php
public function update(Request $request, Course $course, Material $material)
{
    // ... existing validation logic ...
    
    $wasUnpublished = $material->status !== 'published';
    $material->update($validated);
    
    // 🆕 TRIGGER NOTIFICATION: If newly published
    if ($wasUnpublished && $material->status === 'published') {
        $enrolledStudents = $course->enrollments()
            ->where('status', 'active')
            ->with('user')
            ->get()
            ->pluck('user');
        
        foreach ($enrolledStudents as $student) {
            $student->notify(new MaterialPublished($material));
        }
    }
    
    return redirect()->route('guru.courses.materials.index', $course)
        ->with('success', 'Materi berhasil diperbarui');
}
```

---

#### **B. Exam Scheduled (`ExamController`)**

**File:** `app/Http/Controllers/Guru/ExamController.php` (or Admin)

**Add to `store()` method after exam is saved:**

```php
use App\Notifications\ExamScheduled;

public function store(Request $request)
{
    // ... existing validation and save logic ...
    
    $exam = Exam::create($validated);
    
    // 🆕 TRIGGER NOTIFICATION: Send to all enrolled students if published
    if ($exam->status === 'published') {
        $enrolledStudents = $exam->course->enrollments()
            ->where('status', 'active')
            ->with('user')
            ->get()
            ->pluck('user');
        
        foreach ($enrolledStudents as $student) {
            $student->notify(new ExamScheduled($exam));
        }
    }
    
    return redirect()->route('guru.exams.index')
        ->with('success', 'Ujian berhasil dibuat');
}
```

**Also add to `toggleStatus()` method when exam becomes published:**

```php
public function toggleStatus(Exam $exam)
{
    $newStatus = $exam->status === 'published' ? 'draft' : 'published';
    $exam->update(['status' => $newStatus]);
    
    // 🆕 TRIGGER NOTIFICATION: If newly published
    if ($newStatus === 'published') {
        $enrolledStudents = $exam->course->enrollments()
            ->where('status', 'active')
            ->with('user')
            ->get()
            ->pluck('user');
        
        foreach ($enrolledStudents as $student) {
            $student->notify(new ExamScheduled($exam));
        }
    }
    
    return redirect()->back()
        ->with('success', 'Status ujian berhasil diubah');
}
```

---

#### **C. Exam Graded (`ExamAttemptController` or `Guru\ExamController`)**

**File:** `app/Http/Controllers/ExamAttemptController.php`

**Add to `submit()` method after grading:**

```php
use App\Notifications\ExamGraded;

public function submit(Request $request, ExamAttempt $attempt)
{
    // ... existing submission and grading logic ...
    
    $attempt->update([
        'status' => 'graded',
        'submitted_at' => now(),
    ]);
    
    $attempt->autoGrade(); // Auto-grade MCQ and matching
    
    // 🆕 TRIGGER NOTIFICATION: Exam graded
    $attempt->user->notify(new ExamGraded($attempt));
    
    return redirect()->route('siswa.exams.review-attempt', $attempt)
        ->with('success', 'Ujian berhasil diserahkan');
}
```

**File:** `app/Http/Controllers/Guru/ExamController.php`

**Add to `gradeEssay()` method after manual grading:**

```php
public function gradeEssay(Request $request, Exam $exam, \App\Models\Answer $answer)
{
    // ... existing essay grading logic ...
    
    $answer->update([
        'points_earned' => $request->points_earned,
        'feedback' => $request->feedback,
    ]);
    
    // Recalculate attempt score
    $attempt = $answer->attempt;
    $attempt->calculateScore();
    
    // 🆕 TRIGGER NOTIFICATION: If all answers are graded
    if ($attempt->status === 'graded') {
        $attempt->user->notify(new ExamGraded($attempt));
    }
    
    return redirect()->back()
        ->with('success', 'Essay berhasil dinilai');
}
```

---

### **STEP 2: Enhanced Dashboards (Optional but Recommended)**

Create dashboard widgets to show relevant info. This is a nice-to-have for better UX.

#### **Siswa Dashboard Enhancements**

**File:** `resources/views/siswa/dashboard.blade.php`

Add widgets for:
- Upcoming Exams (next 7 days)
- Recent Grades (last 5)
- Course Progress
- Recent Notifications (last 3)

**Example Code:**

```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Dashboard Siswa</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Stat Cards -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Kursus</p>
                    <p class="text-3xl font-bold text-gray-800">{{ auth()->user()->enrolledCourses()->where('enrollments.status', 'active')->count() }}</p>
                </div>
                <i class="fas fa-book text-blue-600 text-3xl"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Ujian Aktif</p>
                    <p class="text-3xl font-bold text-gray-800">
                        {{ \App\Models\Exam::whereIn('course_id', auth()->user()->enrolledCourses()->pluck('id'))
                            ->where('status', 'published')
                            ->whereNull('end_time')
                            ->count() }}
                    </p>
                </div>
                <i class="fas fa-clipboard-list text-green-600 text-3xl"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Rata-rata Nilai</p>
                    <p class="text-3xl font-bold text-gray-800">
                        {{ number_format(auth()->user()->examAttempts()->where('status', 'graded')->avg('score'), 1) }}%
                    </p>
                </div>
                <i class="fas fa-chart-line text-purple-600 text-3xl"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Notifikasi Baru</p>
                    <p class="text-3xl font-bold text-gray-800">{{ auth()->user()->unreadNotifications()->count() }}</p>
                </div>
                <i class="fas fa-bell text-red-600 text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Upcoming Exams Widget -->
    @php
        $upcomingExams = \App\Models\Exam::whereIn('course_id', auth()->user()->enrolledCourses()->pluck('id'))
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('start_time')
                      ->orWhere('start_time', '>=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_time')
                      ->orWhere('end_time', '>=', now());
            })
            ->with('course')
            ->latest()
            ->take(5)
            ->get();
    @endphp
    
    @if($upcomingExams->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">
                <i class="fas fa-calendar-alt text-green-600 mr-2"></i>Ujian Mendatang
            </h2>
            <div class="space-y-3">
                @foreach($upcomingExams as $exam)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800">{{ $exam->title }}</h3>
                            <p class="text-sm text-gray-600">{{ $exam->course->title }}</p>
                            @if($exam->start_time)
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-clock mr-1"></i>{{ $exam->start_time->format('d M Y, H:i') }}
                                </p>
                            @endif
                        </div>
                        <a href="{{ route('siswa.exams.show', $exam) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                            Lihat
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Recent Notifications Widget -->
    @php
        $recentNotifications = auth()->user()->notifications()->latest()->take(3)->get();
    @endphp
    
    @if($recentNotifications->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold">
                    <i class="fas fa-bell text-blue-600 mr-2"></i>Notifikasi Terbaru
                </h2>
                <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Lihat Semua
                </a>
            </div>
            <div class="space-y-3">
                @foreach($recentNotifications as $notification)
                    @php
                        $data = $notification->data;
                    @endphp
                    <div class="flex items-start p-3 bg-gray-50 rounded-lg {{ is_null($notification->read_at) ? 'border-l-4 border-blue-600' : '' }}">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-{{ $data['color'] ?? 'blue' }}-100 flex items-center justify-center">
                                <i class="{{ $data['icon'] ?? 'fas fa-bell' }} text-{{ $data['color'] ?? 'blue' }}-600"></i>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-gray-800">{{ $data['message'] ?? 'Notifikasi baru' }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
```

---

## 🧪 Testing Guide

### **Test 1: Material Published Notification**

```
1. Login as Guru
2. Navigate to a course
3. Create a new material with status "Published"
4. Logout, login as Siswa (enrolled in that course)
5. Check notification bell (should have 1 unread)
6. Click notification → should navigate to course detail
```

### **Test 2: Exam Scheduled Notification**

```
1. Login as Guru
2. Create a new exam with status "Published"
3. Logout, login as Siswa (enrolled in that course)
4. Check notification bell (should have 1 unread)
5. Click notification → should navigate to exam detail
```

### **Test 3: Exam Graded Notification**

```
1. Login as Siswa
2. Take and submit an exam (auto-graded)
3. Check notification bell (should have 1 unread notification for graded exam)
4. Click notification → should navigate to exam review page
```

### **Test 4: Notification Management**

```
1. Login as any user
2. Click notification bell
3. Click "Tandai Semua Dibaca"
4. Verify all notifications are now marked as read
5. Navigate to /notifications
6. Delete a notification
7. Verify it's removed from list
```

---

## 📊 Summary

### **Implemented:**
- ✅ Notifications table & migration
- ✅ 3 Notification classes (MaterialPublished, ExamScheduled, ExamGraded)
- ✅ NotificationController (CRUD)
- ✅ Notification bell component with Alpine.js
- ✅ Notifications index page
- ✅ 5 notification routes
- ✅ Navigation integration

### **Needs Manual Implementation:**
- ⚠️ Trigger notifications in controllers (3 locations shown above)
- ⚠️ Optional: Enhanced dashboards with widgets

### **Time Estimate:**
- **Triggering notifications**: ~15-20 minutes (copy-paste code above)
- **Dashboard enhancements**: ~30-45 minutes (optional)

---

## 🎉 Once Complete

After adding notification triggers, Module 6 will be **100% functional**!

**Benefits:**
- Real-time notifications for students
- Beautiful notification UI
- Mark as read/delete functionality
- Auto-refresh every 30 seconds
- Professional UX

**Ready for Production!** ✅

