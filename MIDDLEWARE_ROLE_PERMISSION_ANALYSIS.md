# 🔐 Middleware, Role, Permission & Policy Analysis

## 📋 Executive Summary

Aplikasi menggunakan **custom role-based access control (RBAC)** dengan 3 roles: `admin`, `guru`, `siswa`. Sistem authorization sudah berfungsi dengan baik, namun ada beberapa area yang dapat ditingkatkan untuk konsistensi dan maintainability.

---

## ✅ **Yang Sudah Baik**

### 1. **Middleware System**
- ✅ **CheckRole** - Middleware untuk single role check
- ✅ **CheckMultipleRoles** - Middleware untuk multiple roles check
- ✅ **LoadSchoolTheme** - Load theme berdasarkan school user
- ✅ **TrackUserActivity** - Track user activity untuk audit
- ✅ Semua middleware sudah terdaftar di `bootstrap/app.php`
- ✅ Middleware menggunakan alias yang mudah: `role:admin`, `roles:admin,guru`

### 2. **Role System**
- ✅ Role disimpan di column `role` di tabel `users`
- ✅ User model punya helper methods: `isAdmin()`, `isGuru()`, `isSiswa()`
- ✅ Role display name menggunakan accessor: `getRoleDisplayAttribute()`
- ✅ Dashboard route berdasarkan role: `getDashboardRouteAttribute()`

### 3. **Route Protection**
- ✅ Routes dilindungi dengan middleware: `auth`, `verified`, `role:admin/guru/siswa`
- ✅ Admin routes: `middleware(['auth', 'verified', 'role:admin'])`
- ✅ Guru routes: `middleware(['auth', 'verified', 'role:guru'])`
- ✅ Siswa routes: `middleware(['auth', 'verified', 'role:siswa'])`
- ✅ Guest routes untuk exam token access

### 4. **Authorization Checks**
- ✅ Authorization check di controller menggunakan `abort(403)`
- ✅ Beberapa controller punya private method `authorizeExam()` untuk reuse
- ✅ Check user active status di middleware
- ✅ Check enrollment sebelum take exam
- ✅ Check ownership (guru owns course/exam)

---

## ⚠️ **Masalah yang Ditemukan**

### 1. **Tidak Ada Policy System**
**Masalah:**
- Tidak menggunakan Laravel Policy untuk resource authorization
- Authorization logic tersebar di controller
- Tidak ada centralized authorization logic

**Contoh:**
```php
// Di Controller - Manual check
if ($exam->course->instructor_id !== auth()->id()) {
    abort(403, 'Unauthorized action.');
}
```

**Dampak:**
- Sulit untuk maintain
- Tidak konsisten
- Sulit untuk test
- Tidak bisa menggunakan `authorize()` helper

### 2. **Authorization Tidak Konsisten**
**Masalah:**
- Beberapa controller menggunakan private method (`authorizeExam()`)
- Beberapa controller menggunakan manual check langsung
- Beberapa controller tidak ada authorization check sama sekali

**Contoh:**
```php
// Guru/QuestionController - Menggunakan private method
private function authorizeExam(Exam $exam) {
    if ($exam->course->instructor_id !== auth()->id()) {
        abort(403, 'Unauthorized action.');
    }
}

// Guru/ExamController - Manual check langsung
if ($course->instructor_id !== auth()->id()) {
    abort(403, 'Unauthorized action.');
}
```

### 3. **Incomplete Authorization Checks**
**Masalah:**
- Beberapa method tidak punya authorization check
- Beberapa authorization check tidak lengkap

**Contoh:**
```php
// EnrollmentController.php line 42-44
if (auth()->user()->isGuru() && $course->instructor_id !== auth()->id()) {
    // Missing abort(403)
}
```

### 4. **Tidak Ada Permission System**
**Masalah:**
- Hanya menggunakan role-based, tidak ada permission-based
- Tidak bisa assign permission per user
- Tidak bisa assign multiple permissions ke role

**Contoh:**
- Admin punya full access (tidak bisa di-customize)
- Guru hanya bisa manage own courses (tidak bisa di-customize)
- Siswa hanya bisa take exams (tidak bisa di-customize)

### 5. **Admin Bypass Semua Checks**
**Masalah:**
- Admin bisa akses semua resource tanpa check
- Tidak ada check apakah admin punya akses ke resource tertentu
- Admin bisa edit/delete resource milik guru lain

**Contoh:**
```php
// Admin bisa edit exam milik guru
// Tidak ada check apakah admin punya akses ke exam ini
```

---

## 🔧 **Rekomendasi Perbaikan**

### 1. **Implement Laravel Policy System**

**Manfaat:**
- Centralized authorization logic
- Konsisten di seluruh aplikasi
- Mudah untuk test
- Bisa menggunakan `authorize()` helper

**Contoh Implementation:**
```php
// app/Policies/ExamPolicy.php
class ExamPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isGuru();
    }

    public function view(User $user, Exam $exam): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isGuru()) {
            return $exam->course->instructor_id === $user->id;
        }

        return $exam->course->students->contains($user);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isGuru();
    }

    public function update(User $user, Exam $exam): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isGuru() && $exam->course->instructor_id === $user->id;
    }

    public function delete(User $user, Exam $exam): bool
    {
        return $this->update($user, $exam);
    }
}
```

**Register Policy:**
```php
// app/Providers/AppServiceProvider.php
use App\Models\Exam;
use App\Policies\ExamPolicy;

protected $policies = [
    Exam::class => ExamPolicy::class,
];
```

**Gunakan di Controller:**
```php
// Di Controller
public function edit(Exam $exam)
{
    $this->authorize('update', $exam);
    // ...
}
```

### 2. **Standardize Authorization Checks**

**Rekomendasi:**
- Gunakan Policy untuk resource authorization
- Gunakan `authorize()` helper di controller
- Hapus manual check yang redundant
- Buat trait untuk common authorization logic

**Contoh:**
```php
// app/Http/Controllers/Controller.php
trait AuthorizesResources
{
    protected function authorizeResource($ability, $resource)
    {
        $this->authorize($ability, $resource);
    }
}
```

### 3. **Fix Incomplete Authorization**

**Perbaikan:**
- Tambahkan authorization check di semua method
- Pastikan semua resource dilindungi
- Test authorization untuk semua routes

**Contoh:**
```php
// EnrollmentController.php
public function store(Request $request, Course $course)
{
    // Check authorization
    if (auth()->user()->isGuru() && $course->instructor_id !== auth()->id()) {
        abort(403, 'Anda tidak memiliki akses ke kelas ini.'); // ✅ Fixed
    }
    // ...
}
```

### 4. **Implement Permission System (Optional)**

**Jika diperlukan:**
- Install Spatie Laravel Permission package
- Buat permission untuk setiap action
- Assign permission ke role
- Check permission di Policy

**Contoh:**
```php
// Install package
composer require spatie/laravel-permission

// Create permissions
Permission::create(['name' => 'manage courses']);
Permission::create(['name' => 'manage exams']);
Permission::create(['name' => 'grade students']);

// Assign to role
$adminRole = Role::create(['name' => 'admin']);
$adminRole->givePermissionTo(['manage courses', 'manage exams', 'grade students']);
```

### 5. **Admin Access Control**

**Rekomendasi:**
- Buat Policy yang check admin access
- Admin bisa akses semua resource (current behavior)
- Atau batasi admin access berdasarkan school (multi-tenant)

**Contoh:**
```php
// ExamPolicy.php
public function update(User $user, Exam $exam): bool
{
    // Admin can update all exams
    if ($user->isAdmin()) {
        // Optional: Check if admin belongs to same school
        // return $user->school_id === $exam->course->school_id;
        return true;
    }

    // Guru can only update own exams
    return $user->isGuru() && $exam->course->instructor_id === $user->id;
}
```

---

## 📊 **Current Authorization Flow**

### **Route Level (Middleware)**
```
Request → CheckRole Middleware → Check User Role → Allow/Deny
```

### **Controller Level (Manual Check)**
```
Request → Controller Method → Manual Check → abort(403) if unauthorized
```

### **Recommended Flow (With Policy)**
```
Request → CheckRole Middleware → Controller Method → Policy Check → Allow/Deny
```

---

## 🎯 **Priority Fixes**

### **High Priority:**
1. ✅ Fix incomplete authorization checks (EnrollmentController, dll)
2. ✅ Implement Policy untuk Exam, Course, Material
3. ✅ Standardize authorization checks di semua controller
4. ✅ Test authorization untuk semua routes

### **Medium Priority:**
5. ✅ Implement Policy untuk Question, Enrollment, Certificate
6. ✅ Buat trait untuk common authorization logic
7. ✅ Document authorization rules

### **Low Priority:**
8. ⚠️ Implement Permission System (jika diperlukan)
9. ⚠️ Admin access control per school (multi-tenant)
10. ⚠️ Audit log untuk authorization failures

---

## 🔍 **Testing Checklist**

### **Middleware Tests:**
- [ ] Test CheckRole middleware dengan valid role
- [ ] Test CheckRole middleware dengan invalid role
- [ ] Test CheckMultipleRoles middleware
- [ ] Test user active status check
- [ ] Test guest access

### **Authorization Tests:**
- [ ] Test admin can access all resources
- [ ] Test guru can only access own courses/exams
- [ ] Test siswa can only access enrolled courses
- [ ] Test unauthorized access returns 403
- [ ] Test enrollment check before take exam

### **Policy Tests (After Implementation):**
- [ ] Test Policy untuk setiap resource
- [ ] Test Policy dengan different roles
- [ ] Test Policy dengan ownership checks
- [ ] Test Policy dengan admin bypass

---

## 📝 **Kesimpulan**

**Status:** ✅ **Functional but can be improved**

**Current State:**
- ✅ Middleware system sudah baik
- ✅ Role system sudah baik
- ✅ Route protection sudah baik
- ⚠️ Authorization tidak konsisten
- ⚠️ Tidak ada Policy system
- ⚠️ Beberapa authorization check tidak lengkap

**Recommended Actions:**
1. Fix incomplete authorization checks (High Priority)
2. Implement Policy system (High Priority)
3. Standardize authorization checks (Medium Priority)
4. Document authorization rules (Medium Priority)
5. Consider Permission system (Low Priority)

**Estimated Time:**
- Fix incomplete checks: 2-4 hours
- Implement Policy: 8-16 hours
- Standardize checks: 4-8 hours
- Total: 14-28 hours

---

## 📚 **References**

- [Laravel Authorization Documentation](https://laravel.com/docs/12.x/authorization)
- [Laravel Policy Documentation](https://laravel.com/docs/12.x/authorization#creating-policies)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)

---

**Generated:** {{ date('Y-m-d H:i:s') }}
**Analyzed By:** AI Assistant
**Version:** 1.0 

