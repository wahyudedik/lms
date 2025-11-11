# 🎓 Certificate Seeder Documentation

Complete guide untuk Certificate Seeder yang generate sample certificate data untuk testing.

## 📋 Overview

`CertificateSeeder` adalah database seeder yang automatically generates sample certificates dengan berbagai scenarios untuk testing certificate system.

## ✨ Features

### Auto-Generated Data

Seeder akan otomatis create:
- ✅ Completed enrollments (jika belum ada)
- ✅ Certificates dengan berbagai grades (A, B, C, D)
- ✅ Recent certificates (< 7 days old)
- ✅ Old certificates (6-12 months old)
- ✅ Revoked certificates (~5%)
- ✅ Excellence certificates (Grade A, 20%)
- ✅ Regular certificates (Grade B-C, 60%)
- ✅ Passing certificates (Grade D, 15%)

### Smart Distribution

Certificates didistribusikan secara realistic:

| Grade | Percentage | Description |
|-------|-----------|-------------|
| A | 20% | Excellence (90-100) |
| B | 30% | Good (80-89) |
| C | 30% | Average (70-79) |
| D | 15% | Passing (60-69) |
| Revoked | 5% | Invalid certificates |

### Metadata

Setiap certificate include rich metadata:
```php
'metadata' => [
    'duration_days' => 30,
    'course_level' => 'advanced',
    'course_category' => 'Programming',
    'total_hours' => 40,
    'completion_rate' => 100,
    'achievement' => 'Excellence Award', // untuk A grade
    'honors' => 'With Distinction',      // untuk A grade
]
```

## 🚀 Usage

### Run Certificate Seeder Only

```bash
php artisan db:seed --class=CertificateSeeder
```

### Run Full Database Seeder (includes certificates)

```bash
php artisan db:seed
```

### Fresh Migration + Seed

```bash
php artisan migrate:fresh --seed
```

## 📊 Sample Output

```
🎓 Seeding certificates...
Creating additional completed enrollments...
Found 11 completed enrollments
Creating 2 excellent certificates (Grade A)...
Creating 6 regular certificates (Grade B-C)...
Creating 1 passing certificates (Grade D)...
Making 9 certificates recent...
Making 8 certificates old...
Revoking 1 certificates...

✅ Certificate seeding completed!

+--------------------+-------+
| Metric             | Count |
+--------------------+-------+
| Total Certificates | 9     |
| Excellence (A)     | 2     |
| Regular (B-D)      | 7     |
| Recent (< 7 days)  | 9     |
| Old (> 6 months)   | 8     |
| Revoked            | 1     |
+--------------------+-------+

📋 Sample Certificates:
✅ CERT-2025-CAABETLA - Siswa 9 - Bahasa Inggris Conversation - Grade: D
✅ CERT-2025-EF3WBFJO - Siswa 8 - Bahasa Indonesia - Grade: A
✅ CERT-2025-QIIVTVFZ - Siswa 9 - Pemrograman Web - Grade: A
✅ CERT-2025-JPXOKYQP - Siswa 4 - Sejarah Indonesia - Grade: B
✅ CERT-2025-FXQL8FST - Siswa 10 - Kimia Dasar - Grade: C
```

## 🔧 How It Works

### 1. Check Completed Enrollments

```php
// Seeder checks for completed enrollments
$completedEnrollments = Enrollment::completed()
    ->doesntHave('certificate')
    ->get();

// If < 20, creates more completed enrollments
if ($completedCount < 20) {
    $this->createCompletedEnrollments(20 - $completedCount);
}
```

### 2. Generate Excellence Certificates (20%)

```php
// Grade A, 90-100 score
$this->createCertificate($enrollment, [
    'final_score' => 95,
    'grade' => 'A',
    'metadata' => [
        'achievement' => 'Excellence Award',
        'honors' => 'With Distinction',
    ],
]);
```

### 3. Generate Regular Certificates (60%)

```php
// Grade B-C, 70-89 score
$score = fake()->numberBetween(70, 89);
$grade = $score >= 80 ? 'B' : 'C';
```

### 4. Generate Passing Certificates (15%)

```php
// Grade D, 60-69 score
$this->createCertificate($enrollment, [
    'final_score' => 65,
    'grade' => 'D',
]);
```

### 5. Make Some Recent

```php
// Last 7 days
Certificate::inRandomOrder()
    ->take(10)
    ->update([
        'issue_date' => now()->subDays(rand(1, 7)),
    ]);
```

### 6. Make Some Old

```php
// 6-12 months ago
Certificate::inRandomOrder()
    ->take(8)
    ->update([
        'issue_date' => now()->subDays(rand(180, 365)),
    ]);
```

### 7. Revoke Some (~5%)

```php
Certificate::inRandomOrder()
    ->take($revokeCount)
    ->each(function ($certificate) {
        $certificate->revoke('Found violation of course policies');
    });
```

## 📚 Dependencies

Seeder requires these to exist first:
- ✅ Users (especially with role `siswa`)
- ✅ Courses
- ✅ Enrollments (will auto-create if needed)

That's why in `DatabaseSeeder`:
```php
$this->call([
    UserSeeder::class,          // 1. Users first
    SchoolSeeder::class,        // 2. Schools
    CourseSeeder::class,        // 3. Courses
    MaterialSeeder::class,      // 4. Materials
    ExamSeeder::class,          // 5. Exams
    ForumSeeder::class,         // 6. Forum
    SettingSeeder::class,       // 7. Settings
    CertificateSeeder::class    // 8. Certificates LAST
]);
```

## 🎯 Testing Scenarios

### Test Excellence Certificates

```bash
php artisan tinker
>>> Certificate::where('grade', 'A')->count()
=> 5

>>> Certificate::where('grade', 'A')->first()->metadata
=> [
     "achievement" => "Excellence Award",
     "honors" => "With Distinction",
     ...
   ]
```

### Test Revoked Certificates

```bash
php artisan tinker
>>> Certificate::revoked()->count()
=> 2

>>> Certificate::revoked()->first()->revoke_reason
=> "Found violation of course policies"
```

### Test Recent Certificates

```bash
php artisan tinker
>>> Certificate::where('issue_date', '>=', now()->subDays(7))->count()
=> 10
```

### Test Old Certificates

```bash
php artisan tinker
>>> Certificate::where('issue_date', '<=', now()->subMonths(6))->count()
=> 8
```

## 🔄 Re-running Seeder

### Run Multiple Times

Seeder is smart - hanya create certificates untuk enrollments yang **belum** punya certificate:

```php
Enrollment::completed()
    ->doesntHave('certificate')  // ← Only enrollments WITHOUT certificates
    ->get();
```

Safe untuk run berkali-kali tanpa duplicate!

### Clear and Reseed

Untuk start fresh:

```bash
# Delete all certificates
php artisan tinker
>>> Certificate::truncate();

# Then reseed
php artisan db:seed --class=CertificateSeeder
```

Or fresh migration:

```bash
php artisan migrate:fresh --seed
```

## 📊 Customization

### Adjust Distribution

Edit percentages di seeder:

```php
// Change from 20% to 30%
$excellentCount = (int) ($completedEnrollments->count() * 0.3);

// Change from 60% to 50%
$regularCount = (int) ($completedEnrollments->count() * 0.5);
```

### Add Custom Metadata

Tambah fields ke metadata:

```php
'metadata' => [
    'duration_days' => 30,
    'custom_field' => 'custom_value',
    'special_notes' => 'Top performer!',
],
```

### Change Revoke Reasons

Edit revoke reasons:

```php
$certificate->revoke(fake()->randomElement([
    'Your custom reason 1',
    'Your custom reason 2',
    'Your custom reason 3',
]));
```

## 🐛 Troubleshooting

### Error: No completed enrollments

**Problem:**
```
⚠️  No completed enrollments found
```

**Solution:**
Seeder akan automatically create completed enrollments. Tapi pastikan:
- Ada users dengan role `siswa`
- Ada courses di database

**Manual fix:**
```bash
php artisan tinker
>>> Enrollment::create([
    'user_id' => 1,
    'course_id' => 1,
    'status' => 'completed',
    'progress' => 100,
    'completed_at' => now(),
]);
```

### Error: Duplicate certificate_number

**Problem:**
```
SQLSTATE[23000]: Integrity constraint violation: Duplicate entry
```

**Solution:**
Certificate numbers are auto-generated dan unique. Ini shouldn't happen, tapi jika terjadi:

```bash
# Check duplicates
php artisan tinker
>>> Certificate::select('certificate_number')
    ->groupBy('certificate_number')
    ->havingRaw('COUNT(*) > 1')
    ->get();

# Delete duplicates
>>> Certificate::where('certificate_number', 'DUPLICATE-NUMBER')
    ->orderBy('created_at', 'desc')
    ->skip(1)
    ->delete();
```

### Error: Column not found

**Problem:**
```
SQLSTATE[42S22]: Column not found: is_active
```

**Solution:**
Run migrations first:

```bash
php artisan migrate
```

## 📈 Statistics

Typical output untuk 30 completed enrollments:

| Metric | Expected Count |
|--------|---------------|
| Total | 30 |
| Excellence (A) | 6 (20%) |
| Good (B) | 9 (30%) |
| Average (C) | 9 (30%) |
| Passing (D) | 4-5 (15%) |
| Revoked | 1-2 (5%) |
| Recent (< 7 days) | 10 |
| Old (> 6 months) | 8 |

## 🎯 Best Practices

### 1. Run After User & Course Seeders

Always seed users and courses first:
```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=CourseSeeder
php artisan db:seed --class=CertificateSeeder
```

### 2. Use with Factory

For custom testing:
```php
Certificate::factory()
    ->excellence()
    ->count(5)
    ->create();
```

### 3. Verify Data

After seeding:
```bash
php artisan tinker
>>> Certificate::count()
>>> Certificate::where('grade', 'A')->count()
>>> Certificate::revoked()->count()
```

## 🚀 Quick Start

Complete workflow:

```bash
# 1. Fresh database
php artisan migrate:fresh

# 2. Seed everything
php artisan db:seed

# 3. Check certificates
php artisan tinker
>>> Certificate::count()
>>> Certificate::with(['user', 'course'])->first()

# 4. View in browser
# Visit: http://lms.test/certificates
```

## 📝 Notes

- Certificate numbers are unique and auto-generated
- Certificates linked to real enrollments
- Metadata is customizable per certificate
- Revocation is soft (keeps data, marks invalid)
- All dates are realistic and varied
- Grades calculated from scores automatically

---

**Certificate Seeder - Ready for Testing! 🎓✨**

Generate realistic certificate data dengan satu command!

