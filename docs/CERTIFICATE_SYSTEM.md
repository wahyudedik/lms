# 🎓 Certificate System Documentation

Auto-generated certificates for course completion dalam Laravel LMS.

## 📋 Table of Contents

1. [Features](#features)
2. [Database Structure](#database-structure)
3. [Certificate Generation](#certificate-generation)
4. [PDF Templates](#pdf-templates)
5. [Certificate Verification](#certificate-verification)
6. [API Endpoints](#api-endpoints)
7. [Artisan Commands](#artisan-commands)
8. [Usage Examples](#usage-examples)

## ✨ Features

### Core Features
- ✅ **Auto-generate certificates** when enrollment is completed
- ✅ **Beautiful PDF templates** dengan landscape orientation
- ✅ **Unique certificate numbers** (CERT-YYYY-XXXXXXXX format)
- ✅ **Grade calculation** based on final score (A-F)
- ✅ **Certificate verification** system
- ✅ **Download & View** certificates
- ✅ **Revoke & Restore** certificates (Admin)
- ✅ **Metadata support** (hours, topics, duration, etc)

### Security Features
- ✅ Unique certificate numbers
- ✅ Public verification URL
- ✅ Certificate status tracking
- ✅ Revocation system with reasons
- ✅ QR code for verification (placeholder)

### User Features
- ✅ View all my certificates
- ✅ Download as PDF
- ✅ View in browser
- ✅ Share verification link

### Admin Features
- ✅ View all certificates
- ✅ Filter by status (valid/revoked)
- ✅ Revoke certificates with reason
- ✅ Restore revoked certificates
- ✅ Generate missing certificates
- ✅ Certificate statistics

## 🗄️ Database Structure

### Certificates Table

```sql
CREATE TABLE certificates (
    id BIGINT PRIMARY KEY,
    enrollment_id BIGINT (FK),
    user_id BIGINT (FK),
    course_id BIGINT (FK),
    certificate_number VARCHAR UNIQUE,
    student_name VARCHAR,
    course_title VARCHAR,
    course_description TEXT,
    issue_date DATE,
    completion_date DATE,
    final_score INT,
    grade VARCHAR,
    instructor_name VARCHAR,
    signature TEXT,
    metadata JSON,
    is_valid BOOLEAN DEFAULT TRUE,
    revoked_at TIMESTAMP NULL,
    revoke_reason VARCHAR NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX(certificate_number),
    INDEX(user_id, course_id),
    INDEX(issue_date)
);
```

### Relationships

```php
// Certificate Model
- belongsTo(Enrollment)
- belongsTo(User)
- belongsTo(Course)

// Enrollment Model
- hasOne(Certificate)

// User Model
- hasMany(Certificate)

// Course Model
- hasMany(Certificate)
```

## 🎯 Certificate Generation

### Automatic Generation

Certificate otomatis dibuat ketika enrollment selesai (status = 'completed'):

```php
// When enrollment completed
$enrollment->markAsCompleted();

// Auto-generate certificate
$certificate = app(CertificateService::class)
    ->generateForEnrollment($enrollment);
```

### Manual Generation

```php
use App\Services\CertificateService;

$service = app(CertificateService::class);

// Generate for specific enrollment
$certificate = $service->generateForEnrollment($enrollment);

// Generate missing certificates (batch)
$count = $service->generateMissing();
```

### Via Artisan Command

```bash
# Generate missing certificates
php artisan certificates:generate

# Dry run (preview only)
php artisan certificates:generate --dry-run

# Force regenerate all
php artisan certificates:generate --force
```

## 📄 PDF Templates

### Template Location
`resources/views/certificates/template.blade.php`

### Template Features
- 🎨 Beautiful landscape design
- 🏆 Medal and decorative elements
- 📊 Student name, course title, grade
- 📅 Completion & issue dates
- 🎓 Instructor signature section
- 🔢 Unique certificate number
- ✅ QR code placeholder for verification

### Customization

```php
// In template.blade.php
<div class="student-name">{{ $certificate->student_name }}</div>
<div class="course-title">{{ $certificate->course_title }}</div>
<div class="grade-badge">Grade: {{ $certificate->grade }}</div>
```

### PDF Generation

```php
$service = app(CertificateService::class);

// Download PDF
return $service->downloadPDF($certificate);

// Stream PDF (view in browser)
return $service->streamPDF($certificate);

// Save to storage
$path = $service->savePDF($certificate);
```

## 🔍 Certificate Verification

### Public Verification

Anyone can verify certificate authenticity:

**URL Format:**
```
https://yoursite.com/verify-certificate/{certificateNumber}
https://yoursite.com/verify-certificate/CERT-2025-ABCD1234
```

### Verification Response

```json
{
    "certificate_number": "CERT-2025-ABCD1234",
    "student_name": "John Doe",
    "course_title": "Laravel Advanced",
    "completion_date": "2025-01-15",
    "is_valid": true,
    "status": "Valid"
}
```

### Invalid/Revoked

```json
{
    "certificate_number": "CERT-2025-ABCD1234",
    "is_valid": false,
    "revoked_at": "2025-02-01",
    "revoke_reason": "Fraudulent completion",
    "status": "Revoked"
}
```

## 🔌 API Endpoints

### Get User Certificates

```http
GET /api/certificates
Authorization: Bearer {token}

Response:
{
    "success": true,
    "data": [
        {
            "id": 1,
            "certificate_number": "CERT-2025-ABCD1234",
            "course_title": "Laravel Advanced",
            "grade": "A",
            "issue_date": "2025-01-15",
            ...
        }
    ]
}
```

### Verify Certificate

```http
POST /api/certificates/verify
Authorization: Bearer {token}
Content-Type: application/json

{
    "certificate_number": "CERT-2025-ABCD1234"
}

Response:
{
    "success": true,
    "data": {
        "certificate_number": "CERT-2025-ABCD1234",
        "is_valid": true,
        "student_name": "John Doe",
        ...
    }
}
```

## 🎛️ Artisan Commands

### Generate Certificates

```bash
# Generate missing certificates
php artisan certificates:generate

# Options:
--dry-run    # Preview without generating
--force      # Force regenerate all
```

### Command Output

```
🎓 Certificate Generation Started

📊 Current Statistics:
+---------------------+-------+
| Metric              | Count |
+---------------------+-------+
| Total Certificates  | 45    |
| Valid Certificates  | 43    |
| Revoked Certificates| 2     |
| Recent (30 days)    | 12    |
| This Month          | 8     |
+---------------------+-------+

🔄 Generating missing certificates...
✅ Successfully generated 5 certificate(s)!

📊 Updated Statistics:
+---------------------+-------+--------+
| Metric              | Count | Change |
+---------------------+-------+--------+
| Total Certificates  | 50    | +5     |
| Valid Certificates  | 48    | +5     |
| This Month          | 13    | +5     |
+---------------------+-------+--------+
```

## 📖 Usage Examples

### Student View

```php
// In StudentController
public function myCertificates()
{
    $certificates = Certificate::byUser(auth()->id())
        ->with('course')
        ->latest('issue_date')
        ->paginate(10);
    
    return view('student.certificates', compact('certificates'));
}
```

### Download Certificate

```php
// Route
Route::get('/certificates/{certificateNumber}/download', 
    [CertificateController::class, 'download']
)->name('certificates.download');

// In Blade
<a href="{{ route('certificates.download', $certificate->certificate_number) }}"
   class="btn btn-primary">
    Download Certificate
</a>
```

### Admin Revoke Certificate

```php
// In AdminController
public function revokeCertificate(Request $request, Certificate $certificate)
{
    $request->validate([
        'reason' => 'required|string|max:500',
    ]);
    
    $certificate->revoke($request->reason);
    
    return back()->with('success', 'Certificate revoked successfully');
}
```

### Verify Certificate

```php
// Public verification
public function verify(string $certificateNumber)
{
    $certificate = app(CertificateService::class)
        ->verify($certificateNumber);
    
    if (!$certificate) {
        return view('certificates.verify', [
            'found' => false,
            'number' => $certificateNumber
        ]);
    }
    
    return view('certificates.verify', [
        'found' => true,
        'certificate' => $certificate
    ]);
}
```

## 🔒 Security Best Practices

### 1. Certificate Number Generation
```php
// Unique format: CERT-YYYY-RANDOM8
// Example: CERT-2025-A7B3C9D2
```

### 2. Verification
- Public verification URL
- No authentication required
- Read-only operation

### 3. Revocation
- Only admins can revoke
- Reason required
- Permanent audit trail

### 4. Access Control
```php
// Only certificate owner, admin, or instructor can download
if ($certificate->user_id !== auth()->id() &&
    !auth()->user()->hasRole('admin') &&
    $certificate->course->instructor_id !== auth()->id()) {
    abort(403);
}
```

## 📊 Statistics & Reporting

### Get Statistics

```php
$stats = app(CertificateService::class)->getStatistics();

/*
[
    'total' => 150,
    'valid' => 145,
    'revoked' => 5,
    'recent' => 23,
    'this_month' => 18,
]
*/
```

### Course Completion Rate

```php
$course = Course::find($id);
$completionRate = $course->enrollments()
    ->whereHas('certificate')
    ->count() / $course->enrollments()->count() * 100;
```

## 🎨 UI Components

### Certificate Card

```html
<div class="certificate-card">
    <div class="certificate-header">
        <h3>{{ $certificate->course_title }}</h3>
        <span class="grade-badge grade-{{ $certificate->grade }}">
            Grade: {{ $certificate->grade }}
        </span>
    </div>
    
    <div class="certificate-details">
        <p><strong>Certificate No:</strong> {{ $certificate->certificate_number }}</p>
        <p><strong>Issued:</strong> {{ $certificate->issue_date->format('F d, Y') }}</p>
        <p><strong>Score:</strong> {{ $certificate->final_score }}%</p>
    </div>
    
    <div class="certificate-actions">
        <a href="{{ $certificate->download_url }}" class="btn btn-primary">
            <i class="fas fa-download"></i> Download
        </a>
        <a href="{{ $certificate->view_url }}" class="btn btn-secondary">
            <i class="fas fa-eye"></i> View
        </a>
        <a href="{{ $certificate->verification_url }}" class="btn btn-info">
            <i class="fas fa-check"></i> Verify
        </a>
    </div>
</div>
```

## 🚀 Future Enhancements

### Planned Features
- [ ] QR code integration dengan library
- [ ] Multiple certificate templates
- [ ] Digital signatures
- [ ] Blockchain verification
- [ ] Email certificates automatically
- [ ] Social media sharing
- [ ] Certificate badges
- [ ] LinkedIn integration
- [ ] Certificate expiration
- [ ] Renewal system

### Possible Integrations
- QR Code: SimpleSoftwareIO/simple-qrcode
- Digital Signature: phpseclib/phpseclib
- Blockchain: Web3 integration
- Email: Laravel Mail dengan attachments

## 🐛 Troubleshooting

### Certificate Not Generated

```php
// Check if enrollment is completed
if (!$enrollment->isCompleted()) {
    // Mark as completed first
    $enrollment->markAsCompleted();
}

// Generate certificate
$certificate = app(CertificateService::class)
    ->generateForEnrollment($enrollment);
```

### PDF Generation Error

```bash
# Publish dompdf config
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"

# Clear config cache
php artisan config:clear
```

### Permission Issues

```bash
# Ensure storage permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

## 📝 Notes

- Certificates are generated only for completed enrollments
- Certificate numbers are unique and cannot be changed
- Revoked certificates remain in database with audit trail
- PDF templates are customizable via Blade views
- Public verification requires no authentication

## 📚 Related Documentation

- [Enrollment System](./ENROLLMENT_SYSTEM.md)
- [Course Management](./COURSE_MANAGEMENT.md)
- [PDF Generation](./PDF_GENERATION.md)

---

**Made with ❤️ for Laravel LMS**

