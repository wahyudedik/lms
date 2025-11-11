# 🎓 Admin Certificate Settings Guide

Guide lengkap untuk superadmin mengatur certificate templates via UI.

## 📋 Overview

Superadmin sekarang dapat mengatur certificate settings melalui web interface yang user-friendly, tanpa perlu edit config file atau code!

## 🎯 Features

### 1. **Template Selection**
Pilih dari 4 template yang tersedia:
- 🎨 **Classic** - Traditional dengan decorative elements
- 🌈 **Modern** - Contemporary dengan colorful gradients
- 🏛️ **Elegant** - Formal dengan gold accents
- ⬜ **Minimalist** - Clean dengan bold typography

### 2. **Institution Branding**
- Set institution name
- Set director name
- Upload institution logo

### 3. **Color Customization**
- Primary color
- Secondary color
- Accent color
- Color presets (quick apply)

### 4. **Live Preview**
- Preview setiap template sebelum save
- Lihat hasil dalam popup window

## 🚀 How to Access

### Via Admin Dashboard

```
Dashboard → Settings → Certificate Settings
```

Or direct URL:
```
https://yoursite.com/admin/certificate-settings
```

## 📸 Interface Overview

```
┌─────────────────────────────────────────────────────────┐
│  🎓 Certificate Settings             [Reset to Default] │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  🎨 Certificate Template                                │
│  ┌──────────┬──────────┬──────────┬──────────┐         │
│  │ Classic  │  Modern  │ Elegant  │Minimalist│         │
│  │   🏆     │    💎    │   🎖️     │    ✨    │         │
│  │ ✓Selected│          │          │          │         │
│  │[Preview] │[Preview] │[Preview] │[Preview] │         │
│  └──────────┴──────────┴──────────┴──────────┘         │
│                                                          │
│  🏫 Institution Information                             │
│  [Institution Name]  [Director Name]                    │
│  [📤 Upload Logo]                                       │
│                                                          │
│  🎨 Color Customization                                 │
│  [Primary] [Secondary] [Accent]                         │
│  [Default Blue] [Tech Green] [Royal Purple] [Red]       │
│                                                          │
│                            [Cancel] [💾 Save Settings]   │
└─────────────────────────────────────────────────────────┘
```

## 🎨 Template Selection

### Step 1: Choose Template

Klik pada card template yang diinginkan:

**Classic** (Default)
- Purple gradient background
- Medal icon
- Decorative corners
- Best for: General purpose

**Modern**
- Colorful gradients
- Achievement stats boxes
- Data-driven design
- Best for: Tech courses

**Elegant**
- Gold & brown formal design
- Serif typography
- Ornamental elements
- Best for: Academic institutions

**Minimalist**
- Black & white design
- Bold typography
- Clean lines
- Best for: Professional certifications

### Step 2: Preview Template

Klik tombol **[Preview]** untuk melihat template dalam popup window.

Preview akan menampilkan:
- Student name: John Doe (sample)
- Course: Laravel Advanced Course (sample)
- Grade: A
- All styling dan layout

## 🏫 Institution Branding

### Institution Name

```
Field: Institution Name
Default: Your App Name
Example: "Harvard University"
```

Akan ditampilkan di footer certificate sebagai institution name.

### Director Name

```
Field: Director Name
Default: "Academic Director"
Example: "Dr. John Smith"
```

Akan ditampilkan di signature section.

### Institution Logo

**Specifications:**
- Format: PNG, JPG, JPEG
- Max size: 2MB
- Recommended: 150x150px
- Background: Transparent (for PNG)

**Steps to Upload:**
1. Click **Choose File**
2. Select your logo image
3. Click **Save Settings**

**Logo will appear:**
- At the top of certificate (depends on template)
- Replaces default emoji icon

## 🎨 Color Customization

### Custom Colors

Set three main colors:

**Primary Color**
- Default: `#3b82f6` (Blue)
- Used for: Main elements, borders, headings

**Secondary Color**
- Default: `#8b5cf6` (Purple)
- Used for: Gradients, accents

**Accent Color**
- Default: `#ec4899` (Pink)
- Used for: Highlights, badges

### How to Set Custom Colors

**Method 1: Color Picker**
1. Click on color box
2. Choose color from picker
3. Color code updates automatically

**Method 2: Type Hex Code**
- Color code is readonly
- Use color picker to change

### Color Presets

Quick apply popular color schemes:

**Default Blue**
```
Primary: #3b82f6
Secondary: #8b5cf6
Accent: #ec4899
```

**Tech Green**
```
Primary: #059669
Secondary: #10b981
Accent: #34d399
```

**Royal Purple**
```
Primary: #7c3aed
Secondary: #8b5cf6
Accent: #a78bfa
```

**Academic Red**
```
Primary: #dc2626
Secondary: #ef4444
Accent: #f87171
```

Click preset button untuk apply semua colors sekaligus.

## 💾 Saving Settings

### Save Changes

1. Make your changes
2. Click **[💾 Save Settings]**
3. Success message will appear
4. Settings applied immediately

**What happens when you save:**
- ✅ Settings saved to database
- ✅ Cache cleared automatically
- ✅ New certificates use new settings
- ✅ Existing certificates unchanged

### Reset to Default

Click **[Reset to Default]** button:
- All settings reset to defaults
- Logo deleted (if uploaded)
- Colors reset to original
- Template reset to "Classic"

**Confirmation required** - Cannot be undone!

## 🔄 How It Works

### Settings Priority

```
1. Database Settings (via Admin UI)  ← Highest priority
2. Config File (config/certificate.php)
3. Default Values
```

When generating certificate:
```php
// Check database first
$template = Setting::get('certificate_template');

// Fall back to config
$template = $template ?? config('certificate.template');

// Final fallback
$template = $template ?? 'default';
```

### Settings Scope

**Applies to:**
- ✅ All new certificates
- ✅ All users
- ✅ All courses

**Does NOT affect:**
- ❌ Existing generated certificates
- ❌ Downloaded PDFs
- ❌ Certificate numbers

## 📊 Settings Storage

Settings disimpan di table `settings`:

```sql
| key                           | value      | type  | group       |
|-------------------------------|------------|-------|-------------|
| certificate_template          | modern     | text  | certificate |
| certificate_institution_name  | Harvard    | text  | certificate |
| certificate_director_name     | Dr. Smith  | text  | certificate |
| certificate_logo_path         | certs/...  | image | certificate |
| certificate_primary_color     | #3b82f6    | color | certificate |
| certificate_secondary_color   | #8b5cf6    | color | certificate |
| certificate_accent_color      | #ec4899    | color | certificate |
```

## 🎯 Best Practices

### Template Selection

**Choose based on:**
- Your institution type
- Course category
- Target audience
- Brand identity

**Examples:**
- University → Elegant
- Tech Bootcamp → Modern
- Corporate Training → Minimalist
- General LMS → Classic

### Colors

**Tips:**
1. Use your brand colors
2. Ensure good contrast
3. Test readability
4. Consider printing

**Contrast Checker:**
- https://webaim.org/resources/contrastchecker/

### Logo

**Best practices:**
1. Use transparent PNG
2. High resolution (300dpi)
3. Square aspect ratio
4. Simple design (scales better)

## 🐛 Troubleshooting

### Template not updating

**Solution:**
```bash
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

### Logo not showing

**Check:**
1. File uploaded successfully?
2. File size < 2MB?
3. Format is PNG/JPG?
4. Storage link created?

```bash
php artisan storage:link
```

### Colors not applying

**Note:** Colors only apply to **Modern** template by design.

Other templates have fixed color schemes:
- Classic: Purple/Pink
- Elegant: Gold/Brown
- Minimalist: Black/White

### Preview not opening

**Check:**
- Popup blocker disabled
- JavaScript enabled
- Route accessible

## 🔒 Security

### Access Control

Only users with **admin** role can access:
```
Middleware: ['auth', 'role:admin']
Route: /admin/certificate-settings
```

### File Upload Security

- ✅ File type validation (PNG, JPG only)
- ✅ File size limit (2MB max)
- ✅ Stored in secure location
- ✅ Auto-delete old files

### Input Validation

All inputs validated:
- Template: Must be valid option
- Colors: Must be valid hex (#RRGGBB)
- Names: Max 255 characters
- Logo: Image validation

## 📱 Mobile Support

Interface is responsive:
- ✅ Works on desktop
- ✅ Works on tablet
- ✅ Works on mobile

Template cards stack vertically on small screens.

## 🎓 Training Guide

### For Administrators

**Quick Setup (5 minutes):**
1. Go to Certificate Settings
2. Choose your preferred template
3. Upload your logo
4. Set institution name
5. Save

**Done!** New certificates will use your settings.

### For Support Staff

**Common Tasks:**

**Change template:**
1. Open Certificate Settings
2. Click different template card
3. Click Preview to verify
4. Click Save Settings

**Update branding:**
1. Open Certificate Settings
2. Update Institution Name field
3. Update Director Name field
4. Upload new logo (optional)
5. Click Save Settings

**Customize colors:**
1. Open Certificate Settings
2. Click color boxes
3. Choose from picker
4. Or click a preset button
5. Click Save Settings

## 📈 Future Enhancements

Planned features:
- [ ] Multiple templates per course
- [ ] Custom template upload
- [ ] More color presets
- [ ] Font selection
- [ ] Template builder (drag & drop)
- [ ] A/B testing

## 📞 Support

**Need Help?**

1. Check this guide first
2. Try Reset to Default
3. Clear caches
4. Contact technical support

**Common Questions:**

Q: Can I create custom templates?
A: Yes, but requires code. See `docs/CERTIFICATE_CUSTOMIZATION.md`

Q: Can different courses use different templates?
A: Not yet. All certificates use the same template. (Future feature!)

Q: Will this affect old certificates?
A: No. Only new certificates use new settings.

---

**Admin Certificate Settings - Complete! 🎉**

Easy certificate customization for superadmin via beautiful web interface!

