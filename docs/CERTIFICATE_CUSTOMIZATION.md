# 🎨 Certificate Template Customization Guide

Complete guide untuk customize certificate templates di Laravel LMS.

## 📋 Table of Contents

1. [Available Templates](#available-templates)
2. [Choosing a Template](#choosing-a-template)
3. [Customizing Colors](#customizing-colors)
4. [Adding Your Logo](#adding-your-logo)
5. [Creating Custom Template](#creating-custom-template)
6. [Configuration Options](#configuration-options)
7. [Advanced Customization](#advanced-customization)

---

## 🎭 Available Templates

### 1. **Default Template** (Classic)
**Location:** `resources/views/certificates/template.blade.php`

**Design Features:**
- 🎨 Purple gradient background
- 🏆 Medal icon
- 🖼️ Decorative corner borders
- 📐 Landscape orientation
- ✨ Professional and festive

**Best For:** General purpose, celebrations

**Preview:**
```
┌──────────────────────────────────────┐
│          🏆 CERTIFICATE              │
│        of Achievement                │
│                                      │
│      [Student Name]                  │
│                                      │
│    [Course Title]                    │
│      Grade: A                        │
│                                      │
│  [Signatures]          [Signatures]  │
└──────────────────────────────────────┘
```

---

### 2. **Modern Template**
**Location:** `resources/views/certificates/templates/modern.blade.php`

**Design Features:**
- 🌈 Colorful gradient background
- 💎 Modern rounded corners
- 🎯 Achievement boxes with stats
- 📊 Data-driven design
- ✨ Clean and contemporary

**Best For:** Tech courses, modern brands

**Colors:**
- Primary: Blue (#3b82f6)
- Secondary: Purple (#8b5cf6)
- Accent: Pink (#ec4899)

**Preview:**
```
┌──────────────────────────────────────┐
│  🎓  CERTIFICATE                     │
│     of Excellence                    │
│                                      │
│      [Student Name]                  │
│                                      │
│    [Course Title]                    │
│         ┌─────┐                      │
│         │  A  │                      │
│         └─────┘                      │
│  ┌─────────────────────────────┐    │
│  │ Date | Score | Duration     │    │
│  └─────────────────────────────┘    │
└──────────────────────────────────────┘
```

---

### 3. **Elegant Template**
**Location:** `resources/views/certificates/templates/elegant.blade.php`

**Design Features:**
- 🏛️ Classic formal design
- 🥇 Gold accents and borders
- 📜 Serif typography
- 🎭 Ornamental elements
- ✨ Premium feel

**Best For:** Academic institutions, formal certifications

**Colors:**
- Primary: Dark brown (#2c1810)
- Accent: Gold (#d4af37)
- Background: Cream (#f5f5f0)

**Preview:**
```
┌──────────────────────────────────────┐
│ ❖               CERTIFICATE        ❖ │
│     ╔═══════════════════════════╗    │
│     ║       🎓                  ║    │
│     ║    Certificate            ║    │
│     ║   ───────────────         ║    │
│     ║  [Student Name]           ║    │
│     ║  [Course Title]           ║    │
│     ║        [A]                ║    │
│     ║  ___________  __________  ║    │
│     ║  Instructor   Director    ║    │
│     ╚═══════════════════════════╝    │
│ ❖                                  ❖ │
└──────────────────────────────────────┘
```

---

### 4. **Minimalist Template**
**Location:** `resources/views/certificates/templates/minimalist.blade.php`

**Design Features:**
- ⬜ Clean white background
- ⬛ Bold black typography
- 📏 Simple lines and borders
- 🎯 Focus on content
- ✨ Swiss design inspired

**Best For:** Modern tech companies, startups

**Colors:**
- Text: Black (#000)
- Accent: Black (#000)
- Background: White (#fff)

**Preview:**
```
┌──────────────────────────────────────┐
│█                                      │
│█  CERTIFICATE                         │
│█  of completion                       │
│█                                      │
│█  [Student Name]                      │
│█                                      │
│█  [Course Title]                      │
│█                                      │
│█  ┌───┐                               │
│█  │ A │                               │
│█  └───┘                               │
│█  ────────────────────────────────    │
│█  Instructor    |    Director         │
└──────────────────────────────────────┘
```

---

## 🔧 Choosing a Template

### Method 1: Via Config File

Edit `config/certificate.php`:

```php
'template' => 'modern',  // Options: default, modern, elegant, minimalist
```

### Method 2: Via Environment Variable

Add to `.env`:

```env
CERTIFICATE_TEMPLATE=modern
```

### Method 3: Programmatically

```php
use App\Services\CertificateService;

$service = app(CertificateService::class);

// Generate with specific template
$pdf = $service->generatePDF($certificate, 'elegant');
```

### Method 4: Per Certificate

Store template preference in certificate metadata:

```php
$certificate->update([
    'metadata' => [
        'template' => 'minimalist',
        // ... other metadata
    ]
]);
```

---

## 🎨 Customizing Colors

### Global Color Configuration

Edit `config/certificate.php`:

```php
'colors' => [
    'primary' => '#3b82f6',    // Blue
    'secondary' => '#8b5cf6',  // Purple
    'accent' => '#ec4899',     // Pink
    'text' => '#1e293b',       // Dark
],
```

Or via `.env`:

```env
CERTIFICATE_PRIMARY_COLOR=#3b82f6
CERTIFICATE_SECONDARY_COLOR=#8b5cf6
CERTIFICATE_ACCENT_COLOR=#ec4899
CERTIFICATE_TEXT_COLOR=#1e293b
```

### Template-Specific Colors

Edit the template file directly:

**Example: `modern.blade.php`**

```css
/* Find and replace colors */
background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
```

**Color Schemes:**

```css
/* Corporate Blue */
Primary: #0052CC
Secondary: #0065FF
Accent: #4C9AFF

/* Academic Red */
Primary: #991B1E
Secondary: #C5050C
Accent: #E71D36

/* Tech Green */
Primary: #059669
Secondary: #10B981
Accent: #34D399

/* Professional Purple */
Primary: #7C3AED
Secondary: #8B5CF6
Accent: #A78BFA
```

---

## 🖼️ Adding Your Logo

### Step 1: Upload Logo

Place logo in `public/images/`:
```
public/images/logo.png
```

### Step 2: Configure Logo Path

Edit `config/certificate.php`:

```php
'institution' => [
    'name' => 'Your Institution Name',
    'logo' => 'images/logo.png',
    'director' => 'Dr. John Doe',
],
```

Or via `.env`:

```env
CERTIFICATE_INSTITUTION_NAME="Harvard University"
CERTIFICATE_LOGO_PATH="images/logo.png"
CERTIFICATE_DIRECTOR_NAME="Dr. John Smith"
```

### Step 3: Update Template

**Modern Template Example:**

```html
<div class="logo-placeholder">
    @if(config('certificate.institution.logo'))
        <img src="{{ public_path(config('certificate.institution.logo')) }}" 
             alt="Logo" style="max-width: 100px; max-height: 100px;">
    @else
        🎓
    @endif
</div>
```

### Logo Specifications

| Template | Recommended Size | Format | Background |
|----------|-----------------|--------|------------|
| Default | 150x150px | PNG | Transparent |
| Modern | 120x120px | PNG/SVG | Any |
| Elegant | 100x100px | PNG | Transparent |
| Minimalist | 80x80px | SVG | None |

---

## 🎨 Creating Custom Template

### Step 1: Create Template File

Create file: `resources/views/certificates/templates/custom.blade.php`

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate - {{ $certificate->certificate_number }}</title>
    <style>
        @page { margin: 0; }
        
        body {
            font-family: Arial, sans-serif;
            padding: 50px;
        }
        
        .certificate-container {
            border: 5px solid #000;
            padding: 60px;
            text-align: center;
        }
        
        /* Your custom styles here */
    </style>
</head>
<body>
    <div class="certificate-container">
        <h1>CERTIFICATE</h1>
        <h2>{{ $certificate->student_name }}</h2>
        <p>{{ $certificate->course_title }}</p>
        <p>Grade: {{ $certificate->grade }}</p>
    </div>
</body>
</html>
```

### Step 2: Register Template

Edit `app/Services/CertificateService.php`:

```php
$templatePath = match($template) {
    'modern' => 'certificates.templates.modern',
    'elegant' => 'certificates.templates.elegant',
    'minimalist' => 'certificates.templates.minimalist',
    'custom' => 'certificates.templates.custom',  // Add this
    default => 'certificates.template',
};
```

### Step 3: Use Custom Template

```php
$pdf = $service->generatePDF($certificate, 'custom');
```

---

## ⚙️ Configuration Options

### Complete Configuration Reference

```php
// config/certificate.php

return [
    // Template selection
    'template' => 'modern',
    
    // Institution info
    'institution' => [
        'name' => 'Your Institution',
        'logo' => 'images/logo.png',
        'director' => 'Director Name',
    ],
    
    // Grade thresholds
    'grades' => [
        'A' => ['min' => 90, 'label' => 'Excellent'],
        'B' => ['min' => 80, 'label' => 'Very Good'],
        'C' => ['min' => 70, 'label' => 'Good'],
        'D' => ['min' => 60, 'label' => 'Satisfactory'],
        'F' => ['min' => 0, 'label' => 'Needs Improvement'],
    ],
    
    // Certificate number format
    'number_format' => 'CERT-{YEAR}-{RANDOM}',
    'random_length' => 8,
    
    // PDF settings
    'pdf' => [
        'paper' => 'a4',
        'orientation' => 'landscape',
        'dpi' => 150,
    ],
    
    // Colors
    'colors' => [
        'primary' => '#3b82f6',
        'secondary' => '#8b5cf6',
        'accent' => '#ec4899',
        'text' => '#1e293b',
    ],
    
    // Auto-generation
    'auto_generate' => [
        'enabled' => true,
        'on_completion' => true,
        'notify_student' => true,
    ],
    
    // Storage
    'storage' => [
        'disk' => 'public',
        'path' => 'certificates',
        'auto_save' => false,
    ],
];
```

---

## 🚀 Advanced Customization

### 1. Dynamic Content

```php
// In template
@if(isset($certificate->metadata['honors']))
    <div class="honors-badge">
        🏆 {{ $certificate->metadata['honors'] }}
    </div>
@endif
```

### 2. Conditional Styling

```php
<div class="grade-badge" style="
    background: {{ $certificate->grade === 'A' ? '#10b981' : '#6b7280' }};
">
    {{ $certificate->grade }}
</div>
```

### 3. Multiple Signatures

```php
@foreach($certificate->metadata['signatures'] ?? [] as $signature)
    <div class="signature-box">
        <div class="signature-line"></div>
        <div>{{ $signature['name'] }}</div>
        <div>{{ $signature['title'] }}</div>
    </div>
@endforeach
```

### 4. Watermarks

```css
.watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-45deg);
    font-size: 120px;
    color: rgba(0,0,0,0.03);
    z-index: 0;
}
```

```html
<div class="watermark">CERTIFIED</div>
```

### 5. Custom Fonts

```css
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap');

body {
    font-family: 'Playfair Display', serif;
}
```

### 6. QR Code Integration

```php
// In CertificateService
protected function generateQRCode(Certificate $certificate): string
{
    return QrCode::size(200)
        ->generate($certificate->verification_url);
}
```

```html
<!-- In template -->
<div class="qr-code">
    {!! $qrCode !!}
</div>
```

---

## 📱 Responsive Design Tips

### For Print

```css
@media print {
    body {
        margin: 0;
        padding: 0;
    }
    
    .no-print {
        display: none;
    }
}
```

### For Different Paper Sizes

```php
// config/certificate.php
'pdf' => [
    'paper' => 'letter',  // or 'a4', 'legal'
    'orientation' => 'portrait',  // or 'landscape'
],
```

---

## 🎯 Best Practices

### 1. Typography

```css
/* Good font hierarchy */
.certificate-title { font-size: 48px; }  /* Main title */
.student-name { font-size: 36px; }       /* Emphasis */
.course-title { font-size: 24px; }       /* Secondary */
.details { font-size: 14px; }            /* Body text */
```

### 2. Color Contrast

- Minimum contrast ratio: 4.5:1
- Test with: https://webaim.org/resources/contrastchecker/

### 3. Layout

- Keep margins: 30-50px
- Center important content
- Use white space effectively
- Align text properly

### 4. File Size

- Optimize images
- Use web fonts sparingly
- Keep CSS minimal
- Target < 500KB per PDF

---

## 🐛 Troubleshooting

### Issue: Logo not showing

**Solution:**
```php
// Use absolute path
<img src="{{ public_path('images/logo.png') }}" />
```

### Issue: Colors not rendering

**Solution:**
```bash
php artisan config:clear
php artisan view:clear
```

### Issue: PDF generation slow

**Solution:**
```php
// Reduce DPI in config
'dpi' => 96,  // instead of 150
```

### Issue: Template not found

**Solution:**
```bash
# Clear cache
php artisan view:clear
php artisan config:clear
```

---

## 📚 Resources

### Design Inspiration
- [Canva Certificates](https://www.canva.com/certificates/)
- [Behance Certificates](https://www.behance.net/search/projects/certificate)
- [Dribbble Certificates](https://dribbble.com/tags/certificate)

### Color Palettes
- [Coolors.co](https://coolors.co/)
- [Adobe Color](https://color.adobe.com/)
- [Color Hunt](https://colorhunt.co/)

### Fonts
- [Google Fonts](https://fonts.google.com/)
- [Font Squirrel](https://www.fontsquirrel.com/)

---

## 🎉 Template Comparison

| Feature | Default | Modern | Elegant | Minimalist |
|---------|---------|--------|---------|------------|
| Design Style | Classic | Contemporary | Formal | Clean |
| Colors | Multi | Gradient | Gold | B&W |
| Typography | Mixed | Sans-serif | Serif | Sans-serif |
| Decoration | High | Medium | High | Minimal |
| Best For | General | Tech | Academic | Professional |
| Customizable | ⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |

---

**Happy Customizing! 🎨**

