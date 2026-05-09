# Role Equivalence Fix Guide

## Summary of Changes

This guide documents the fixes applied to handle role equivalence in the Laravel LMS project where:
- `dosen` ≡ `guru` (same permissions, different URL prefix)
- `mahasiswa` ≡ `siswa` (same permissions, different URL prefix)

## ✅ Completed Fixes

### 1. CheckRole Middleware (`app/Http/Middleware/CheckRole.php`)
**Problem**: Used strict equality (`$user->role !== $role`) which prevented equivalent roles from accessing routes.

**Solution**: Implemented role equivalence mapping using `match` expression:
```php
$allowedRoles = match ($role) {
    'guru' => ['guru', 'dosen'],
    'dosen' => ['guru', 'dosen'],
    'siswa' => ['siswa', 'mahasiswa'],
    'mahasiswa' => ['siswa', 'mahasiswa'],
    default => [$role],
};

if (!in_array($user->role, $allowedRoles, true)) {
    abort(403, 'Anda tidak memiliki akses ke halaman ini.');
}
```

**Impact**: Dosen can now access guru routes, mahasiswa can access siswa routes.

### 2. Blade Directive for Dynamic Routes (`app/Providers/AppServiceProvider.php`)
**Problem**: Blade views had hardcoded routes like `route('siswa.courses.index')` which broke for mahasiswa users.

**Solution**: Created `@roleRoute` Blade directive that automatically uses the authenticated user's role prefix:
```php
\Illuminate\Support\Facades\Blade::directive('roleRoute', function ($expression) {
    return "<?php echo route(auth()->user()->getRolePrefix() . '.' . {$expression}); ?>";
});
```

**Usage**:
```blade
{{-- OLD (hardcoded) --}}
<a href="{{ route('siswa.courses.index') }}">Courses</a>

{{-- NEW (dynamic) --}}
<a href="{{ @roleRoute('courses.index') }}">Courses</a>
```

### 3. Controller Trait (`app/Http/Controllers/Concerns/ResolvesRolePrefix.php`)
**Already implemented** - Provides helper methods for controllers:
- `teacherRoute($suffix, $params)` - Generates guru/dosen routes
- `studentRoute($suffix, $params)` - Generates siswa/mahasiswa routes
- `userRoute($suffix, $params)` - Generates routes for current user

## 🔧 Remaining Work: Update Blade Views

### Files That Need Updates

#### Siswa Views (High Priority)
All files in `resources/views/siswa/` with hardcoded `route('siswa.xxx')` calls:

1. **Reports**
   - `siswa/reports/my_transcript.blade.php` (7 occurrences)
   - `siswa/reports/index.blade.php` (4 occurrences)

2. **Materials**
   - `siswa/materials/show.blade.php` (2 occurrences)
   - `siswa/materials/index.blade.php` (3 occurrences)

3. **Grades**
   - `siswa/grades/show.blade.php` (4 occurrences)
   - `siswa/grades/index.blade.php` (3 occurrences)

4. **Exams**
   - `siswa/exams/show.blade.php` (2 occurrences)
   - `siswa/exams/take.blade.php` (1 occurrence)
   - `siswa/exams/review-attempt.blade.php` (likely has occurrences)
   - `siswa/exams/index.blade.php` (likely has occurrences)

5. **Courses**
   - `siswa/courses/show.blade.php` (likely has occurrences)
   - `siswa/courses/index.blade.php` (likely has occurrences)

6. **Assignments**
   - `siswa/assignments/*.blade.php` (likely has occurrences)

7. **Certificates**
   - `siswa/certificates/*.blade.php` (likely has occurrences)

#### Guru Views (Medium Priority)
All files in `resources/views/guru/` with hardcoded `route('guru.xxx')` calls:

1. **Reports**
   - `guru/reports/index.blade.php` (5 occurrences)

2. **Questions**
   - `guru/questions/index.blade.php` (5 occurrences)
   - `guru/questions/edit.blade.php` (2 occurrences)
   - `guru/questions/create.blade.php` (2 occurrences)

3. **Materials**
   - `guru/materials/show.blade.php` (3 occurrences)
   - Other material views (likely have occurrences)

4. **Exams, Courses, Assignments**
   - Multiple files (need to be checked)

### Migration Pattern

#### Simple Routes (No Parameters)
```blade
{{-- BEFORE --}}
<a href="{{ route('siswa.courses.index') }}">View Courses</a>
<form action="{{ route('siswa.materials.index') }}" method="GET">

{{-- AFTER --}}
<a href="{{ @roleRoute('courses.index') }}">View Courses</a>
<form action="{{ @roleRoute('materials.index') }}" method="GET">
```

#### Routes with Parameters
```blade
{{-- BEFORE --}}
<a href="{{ route('siswa.courses.show', $course) }}">View Course</a>
<a href="{{ route('siswa.exams.review-attempt', $attempt) }}">Review</a>
<form action="{{ route('siswa.materials.comment', $material) }}" method="POST">

{{-- AFTER --}}
<a href="{{ route(auth()->user()->getRolePrefix() . '.courses.show', $course) }}">View Course</a>
<a href="{{ route(auth()->user()->getRolePrefix() . '.exams.review-attempt', $attempt) }}">Review</a>
<form action="{{ route(auth()->user()->getRolePrefix() . '.materials.comment', $material) }}" method="POST">
```

**Note**: For routes with parameters, we use the full `auth()->user()->getRolePrefix()` approach since Blade directives can't easily handle parameters.

### Automated Search & Replace Strategy

Use these regex patterns to find and replace:

1. **Find simple routes**:
   ```regex
   route\('(siswa|guru)\.([\w.]+)'\)
   ```
   Replace with:
   ```blade
   @roleRoute('$2')
   ```

2. **Find routes with parameters** (manual review needed):
   ```regex
   route\('(siswa|guru)\.([\w.]+)',\s*(.+?)\)
   ```
   Replace with:
   ```blade
   route(auth()->user()->getRolePrefix() . '.$2', $3)
   ```

## Testing Checklist

After updating views, test the following scenarios:

### As Siswa User
- [ ] Navigate to all siswa routes
- [ ] Click all links in siswa views
- [ ] Submit all forms in siswa views
- [ ] Verify no 404 errors

### As Mahasiswa User
- [ ] Navigate to all mahasiswa routes (should use siswa controllers)
- [ ] Click all links (should generate mahasiswa URLs)
- [ ] Submit all forms (should post to mahasiswa routes)
- [ ] Verify no 404 errors

### As Guru User
- [ ] Navigate to all guru routes
- [ ] Click all links in guru views
- [ ] Submit all forms in guru views
- [ ] Verify no 404 errors

### As Dosen User
- [ ] Navigate to all dosen routes (should use guru controllers)
- [ ] Click all links (should generate dosen URLs)
- [ ] Submit all forms (should post to dosen routes)
- [ ] Verify no 404 errors

## Architecture Notes

### Why This Approach?

1. **Middleware Fix**: Allows equivalent roles to access each other's routes at the middleware level
2. **Blade Directive**: Provides clean syntax for simple routes without parameters
3. **Controller Trait**: Already implemented for server-side redirects
4. **User Model Helper**: `getRolePrefix()` method provides the foundation

### Alternative Approaches Considered

1. **Route Aliasing**: Would require duplicating all routes - rejected due to maintenance burden
2. **View Sharing**: Would require separate view folders for each role - rejected as views are identical
3. **JavaScript Route Generation**: Would require client-side logic - rejected for simplicity

### Future Improvements

1. **Blade Component**: Create a `<x-role-link>` component for cleaner syntax
2. **Route Helper**: Create a global `roleRoute()` helper function
3. **Automated Testing**: Add feature tests for role equivalence
4. **Documentation**: Update developer docs with role equivalence patterns

## Quick Reference

### User Model Methods
```php
$user->isGuru()        // true for guru only
$user->isDosen()       // true for dosen only
$user->isSiswa()       // true for siswa only
$user->isMahasiswa()   // true for mahasiswa only
$user->getRolePrefix() // returns 'guru', 'dosen', 'siswa', or 'mahasiswa'
```

### Controller Trait Methods
```php
$this->teacherRoute('courses.index')              // guru.courses.index or dosen.courses.index
$this->studentRoute('exams.show', $exam)          // siswa.exams.show or mahasiswa.exams.show
$this->userRoute('dashboard')                     // current user's dashboard route
```

### Blade Directives
```blade
@roleRoute('courses.index')                       {{-- Simple routes only --}}
{{ route(auth()->user()->getRolePrefix() . '.courses.show', $course) }}  {{-- With parameters --}}
```

## Status Summary

✅ **Completed**:
- Middleware role equivalence
- Blade directive for simple routes
- Controller trait (already existed)
- User model helpers (already existed)

⚠️ **In Progress**:
- Updating Blade views (manual work required)

📋 **Pending**:
- Comprehensive testing
- Documentation updates
- Feature tests for role equivalence
