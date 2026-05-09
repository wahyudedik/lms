# Blade Routes Update Status

## ✅ Completed Updates

### Core Infrastructure
1. **CheckRole Middleware** - ✅ Updated to support role equivalence
2. **AppServiceProvider** - ✅ Added `@roleRoute` Blade directive
3. **ResolvesRolePrefix Trait** - ✅ Already exists for controllers

### Blade Views Updated
1. **resources/views/siswa/reports/my_transcript.blade.php** - ✅ All 4 routes updated
   - Form action: `reports.my-transcript`
   - PDF export: `reports.my-transcript-pdf`
   - Review attempt: `exams.review-attempt`
   - Show exam: `exams.show`

## 📋 Remaining Files to Update

### High Priority - Siswa Views (Student-facing)

#### Reports (1 file remaining)
- [ ] `siswa/reports/index.blade.php` (4 occurrences)

#### Materials (2 files)
- [ ] `siswa/materials/show.blade.php` (2 occurrences)
- [ ] `siswa/materials/index.blade.php` (3 occurrences)

#### Grades (2 files)
- [ ] `siswa/grades/show.blade.php` (5 occurrences)
- [ ] `siswa/grades/index.blade.php` (4 occurrences)

#### Exams (4+ files)
- [ ] `siswa/exams/show.blade.php` (multiple occurrences)
- [ ] `siswa/exams/take.blade.php` (1 occurrence)
- [ ] `siswa/exams/index.blade.php` (estimated 5+ occurrences)
- [ ] `siswa/exams/review-attempt.blade.php` (estimated 3+ occurrences)

#### Courses, Assignments, Certificates, Dashboard
- [ ] Multiple files (estimated 30+ files remaining)

### Medium Priority - Guru Views (Teacher-facing)
- [ ] Multiple files (estimated 35+ files)

## 🔧 Update Pattern

### For Routes WITH Parameters
```blade
{{-- BEFORE --}}
<a href="{{ route('siswa.courses.show', $course) }}">

{{-- AFTER --}}
<a href="{{ route(auth()->user()->getRolePrefix() . '.courses.show', $course) }}">
```

## 📊 Estimated Totals

- **Siswa Views**: ~40 files, ~150+ route occurrences
- **Guru Views**: ~35 files, ~120+ route occurrences
- **Total**: ~75 files, ~270+ route occurrences
- **Completed**: 1 file, 4 routes
- **Remaining**: ~74 files, ~266+ routes

## 🎯 Recommended Approach

Use find/replace in your IDE with regex:
- **Find**: `route\('(siswa|guru)\.([^']+)',`
- **Replace**: `route(auth()->user()->getRolePrefix() . '.$2',`

## ✅ Testing Checklist

Test with all 4 roles after updates:
- [ ] Siswa - URLs should contain `/siswa/`
- [ ] Mahasiswa - URLs should contain `/mahasiswa/`
- [ ] Guru - URLs should contain `/guru/`
- [ ] Dosen - URLs should contain `/dosen/`
