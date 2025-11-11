# 📚 Built-in Documentation System

Complete guide untuk system dokumentasi yang terintegrasi di Laravel LMS.

## 📋 Overview

Documentation System adalah fitur built-in yang memungkinkan administrator untuk:
- ✅ Access semua dokumentasi dari admin panel
- ✅ Beautiful markdown rendering
- ✅ Auto-categorization
- ✅ Search & navigation
- ✅ Print & download support

## 🚀 Quick Start

### Access Documentation

**Via Admin Menu:**
```
Admin → Profile Dropdown → Documentation
```

**Direct URL:**
```
http://lms.test/admin/documentation
```

### Features

1. **Auto-Indexing** - All `.md` files in `docs/` folder automatically indexed
2. **Categorization** - Smart categorization by filename
3. **Beautiful Rendering** - Markdown parsed dengan Parsedown library
4. **Navigation** - Sidebar navigation & prev/next links
5. **Quick Access** - Direct links to related features
6. **Stats** - Real-time file statistics

## 📁 File Structure

```
docs/
├── CERTIFICATE_SYSTEM.md          → Certificates category
├── CERTIFICATE_CUSTOMIZATION.md   → Certificates category
├── ADMIN_CERTIFICATE_SETTINGS.md  → Certificates category
├── CERTIFICATE_SEEDER.md          → Certificates category
├── OFFLINE_MODE.md                → Offline Mode category
├── OFFLINE_MODE_SUMMARY.md        → Offline Mode category
├── LANDING-PAGE-FEATURE.md        → Landing Page category
└── DOCUMENTATION_SYSTEM.md        → General category
```

## 🎯 How It Works

### 1. File Detection

Controller scans `docs/` folder for `.md` files:

```php
$files = File::files(base_path('docs'));
```

### 2. Auto-Categorization

Smart categorization based on filename:

```php
private function getCategoryFromFilename($filename)
{
    $name = strtolower($filename);
    
    if (str_contains($name, 'certificate')) {
        return 'Certificates';
    }
    
    if (str_contains($name, 'offline')) {
        return 'Offline Mode';
    }
    
    // ... more categories
    
    return 'General';
}
```

### 3. Metadata Extraction

Extract title, description dari markdown:

```php
// Title from first H1
preg_match('/^#\s+(.+)$/m', $content, $matches);
$title = $matches[1] ?? $filename;

// Description from first paragraph
preg_match('/^#.+\n\n(.+?)(\n\n|$)/s', $content, $descMatches);
$description = $descMatches[1] ?? 'Documentation guide';
```

### 4. Markdown Parsing

Using Parsedown library:

```php
$parsedown = new \Parsedown();
$html = $parsedown->text($content);
```

## 🎨 UI Features

### Documentation Index

**Layout:**
```
┌─────────────────────────────────────────┐
│  📚 System Documentation                │
│  Complete guides for optimization       │
├─────────────────────────────────────────┤
│                                         │
│  📊 Stats Overview                      │
│  [Total] [Certificates] [Offline] [...]│
│                                         │
│  📁 Certificates (4 documents)          │
│  ├─ Certificate System Guide            │
│  ├─ Certificate Customization           │
│  ├─ Admin Certificate Settings          │
│  └─ Certificate Seeder Guide            │
│                                         │
│  📁 Offline Mode (2 documents)          │
│  ├─ Offline Mode Complete Guide         │
│  └─ Offline Mode Summary                │
│                                         │
│  🔗 Quick Access                        │
│  [Certificate Settings] [Offline Exams] │
└─────────────────────────────────────────┘
```

### Documentation View

**Features:**
- Sidebar navigation
- Print button
- Download button
- Prev/Next navigation
- Beautiful markdown styling
- Code syntax highlighting
- Table formatting
- Image support

**Styles:**
```css
.documentation-content {
    font-size: 16px;
    line-height: 1.7;
}

.documentation-content h1 {
    font-size: 2.5rem;
    border-bottom: 3px solid #6366f1;
}

.documentation-content pre {
    background-color: #1f2937;
    color: #f9fafb;
}
```

## 📝 Writing Documentation

### Markdown Format

**Standard Structure:**

```markdown
# Title

> Short description or tagline

## 📋 Overview

Main overview section...

## 🚀 Quick Start

Getting started guide...

### Feature 1

Details about feature 1...

### Feature 2

Details about feature 2...

## 📚 Advanced Topics

In-depth information...

## 🐛 Troubleshooting

Common issues and solutions...

## 📞 Support

Contact information...
```

### Supported Markdown

**Headers:**
```markdown
# H1
## H2
### H3
#### H4
##### H5
###### H6
```

**Emphasis:**
```markdown
**bold**
*italic*
***bold italic***
```

**Lists:**
```markdown
- Unordered item 1
- Unordered item 2

1. Ordered item 1
2. Ordered item 2
```

**Links:**
```markdown
[Link Text](http://example.com)
```

**Images:**
```markdown
![Alt Text](path/to/image.png)
```

**Code:**
````markdown
Inline `code`

```php
// Code block
function example() {
    return 'Hello World';
}
```
````

**Tables:**
```markdown
| Column 1 | Column 2 |
|----------|----------|
| Data 1   | Data 2   |
```

**Blockquotes:**
```markdown
> This is a blockquote
```

**Horizontal Rules:**
```markdown
---
```

### Best Practices

1. **Clear Titles** - Use descriptive H1 titles
2. **Table of Contents** - Add navigation headers
3. **Code Examples** - Include practical examples
4. **Screenshots** - Visual aids when helpful
5. **Step-by-Step** - Break down complex tasks
6. **Troubleshooting** - Common issues section
7. **Links** - Link to related docs

### Icons & Emojis

Use emojis for visual appeal:

```markdown
## 📋 Overview
## 🚀 Quick Start
## 🎯 Features
## 🔧 Configuration
## 📚 Examples
## 🐛 Troubleshooting
## 📞 Support
```

## 🔧 Technical Details

### Routes

```php
// Documentation index
Route::get('admin/documentation', 
    [DocumentationController::class, 'index'])
    ->name('admin.documentation.index');

// Show specific document
Route::get('admin/documentation/{slug}', 
    [DocumentationController::class, 'show'])
    ->name('admin.documentation.show');
```

### Controller Methods

**index()** - List all documentation

```php
public function index()
{
    $docs = $this->getDocumentationList();
    return view('admin.documentation.index', compact('docs'));
}
```

**show($slug)** - Display specific document

```php
public function show($slug)
{
    $doc = collect($docs)->firstWhere('slug', $slug);
    $content = File::get($doc['path']);
    $html = $this->parseMarkdown($content);
    return view('admin.documentation.show', compact('doc', 'html'));
}
```

### Views

**index.blade.php** - Documentation list
- Stats overview
- Categorized documents
- Quick access links

**show.blade.php** - Document viewer
- Sidebar navigation
- Parsed markdown content
- Print/download buttons
- Prev/next navigation

## 🎨 Customization

### Add New Category

Edit `getCategoryFromFilename()` method:

```php
if (str_contains($name, 'your-keyword')) {
    return 'Your Category';
}
```

### Change Icon

Edit `getIconForCategory()` method:

```php
return match ($category) {
    'Your Category' => 'fa-your-icon',
    // ... existing categories
};
```

### Custom Styling

Modify `.documentation-content` styles in `show.blade.php`:

```css
.documentation-content h1 {
    color: your-color;
    font-size: your-size;
}
```

## 📊 Statistics

### Document Metrics

**Displayed Stats:**
- Total Documents
- Documents per Category
- File Sizes
- Last Modified Dates

**Auto-calculated:**
```php
[
    'total' => count($docs),
    'certificates' => collect($docs)->where('category', 'Certificates')->count(),
    'categories' => collect($docs)->pluck('category')->unique()->count(),
]
```

## 🔒 Security

### Access Control

Only admins can access:

```php
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('documentation', ...);
    });
```

### Safe Markdown

Parsedown safe mode prevents XSS:

```php
$parsedown->setSafeMode(false); // Set to true for user-generated content
```

## 🚀 Adding New Documentation

### Step 1: Create Markdown File

```bash
touch docs/YOUR_FEATURE.md
```

### Step 2: Write Content

```markdown
# Your Feature Name

> Short description

## Overview

Details about your feature...
```

### Step 3: Auto-Indexed!

File automatically appears in documentation list!

**No code changes needed!** 🎉

## 📱 Mobile Support

**Responsive Design:**
- Mobile-optimized layout
- Collapsible sidebar
- Touch-friendly navigation
- Readable font sizes

## 🎯 Use Cases

### For Administrators

**Learn Features:**
- Certificate system configuration
- Offline mode setup
- Forum management
- System optimization

**Reference:**
- API documentation
- Configuration options
- Troubleshooting guides
- Best practices

### For Developers

**Onboarding:**
- System architecture
- Feature implementation
- Code standards
- Testing guidelines

**Maintenance:**
- Update procedures
- Debugging tips
- Performance tuning
- Security practices

## 🔄 Updates

### Automatic

Documentation updates automatically when:
- File content changes
- New files added
- Files removed
- File renamed

**No cache clearing needed!**

### Manual

To force update:
```bash
# Clear view cache
php artisan view:clear

# Clear route cache
php artisan route:clear
```

## 📈 Future Enhancements

**Planned Features:**
- 🔍 Full-text search
- 📑 Export to PDF
- 🌐 Multi-language support
- 📝 Inline editing
- 🔖 Favorites/bookmarks
- 📊 View analytics
- 💬 Comments/feedback
- 🔔 Update notifications

## 📞 Support

**Questions?**
- Read this guide
- Check other documentation
- Contact system admin
- Submit feedback

**Contribute:**
- Write new documentation
- Improve existing docs
- Report errors
- Suggest topics

---

**Documentation System - Complete! 📚✨**

All documentation accessible from admin panel dengan beautiful formatting!

