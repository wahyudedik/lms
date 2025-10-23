# 🎨 **Dynamic Landing Page Feature**

## 📋 **Overview**

The Dynamic Landing Page system allows each school in the LMS to have a fully customized, professional landing page. This feature integrates seamlessly with the existing School and Theme management system.

---

## ✨ **Features**

### **1. Customizable Sections**

- **Hero Section** - Eye-catching header with title, subtitle, description, background image, and CTA button
- **Statistics Section** - Showcase impressive numbers (students, courses, teachers, success rate)
- **Features Section** - Highlight key features with Font Awesome icons
- **About Section** - Tell your school's story with text and optional image
- **Contact Section** - Display contact information (email, phone, WhatsApp, address)
- **Social Media Links** - Connect with Facebook, Instagram, Twitter, YouTube
- **SEO Optimization** - Custom meta title, description, and keywords

### **2. Admin Landing Page Editor**

- **Tabbed Interface** - Organized into 7 tabs: General, Hero, About, Features, Statistics, Contact & Social, SEO
- **Live Preview** - Preview changes before publishing
- **Dynamic Features Management** - Add/remove features dynamically
- **Dynamic Statistics Management** - Add/remove statistics dynamically
- **Image Uploads** - Hero image and About section image support
- **Font Awesome Icons** - Easy icon selection with direct link to FontAwesome
- **Enable/Disable Toggle** - Turn landing page on/off per school

### **3. Smart Fallback**

- If landing page is disabled, shows default Laravel welcome page
- Graceful handling of missing data with sensible defaults
- Automatic JSON encoding/decoding for features and statistics

---

## 📁 **Files Created/Modified**

### **Database**
- `database/migrations/2025_10_23_211517_add_landing_page_fields_to_schools_table.php` - New migration
- `database/seeders/SchoolSeeder.php` - Updated with sample landing page data

### **Models**
- `app/Models/School.php` - Added fillable fields, casts, and helper methods

### **Controllers**
- `app/Http/Controllers/Admin/LandingPageController.php` - New controller for landing page management

### **Views**
- `resources/views/admin/landing-page/edit.blade.php` - New comprehensive editor
- `resources/views/admin/schools/index.blade.php` - Added "Edit Landing Page" button
- `resources/views/welcome.blade.php` - Completely rewritten for dynamic content

### **Routes**
- `routes/web.php` - Added 3 new routes for landing page management

---

## 🚀 **How to Use**

### **For Fresh Installation:**

```bash
# Run migration
php artisan migrate

# Seed with sample data
php artisan migrate:fresh --seed
```

### **For Existing Installation:**

```bash
# Only run migration (preserves existing data)
php artisan migrate

# Update existing schools manually via admin panel
```

### **Admin Access:**

1. Navigate to `/admin/schools`
2. Click the **paint brush icon** (🎨) next to any school
3. Edit landing page content in the tabbed interface
4. Click **Preview** to see changes
5. Click **Save Landing Page** to publish

---

## 🎯 **Usage Examples**

### **1. Enable Landing Page**

Go to **General** tab and check "Enable landing page"

### **2. Customize Hero Section**

Go to **Hero Section** tab:
- Add your title: "Welcome to Our School"
- Add subtitle: "Excellence in Education"
- Upload a stunning hero image (1920x1080px recommended)
- Set CTA button text and link

### **3. Add Features**

Go to **Features** tab:
- Click "Add Feature"
- Enter Font Awesome icon class (e.g., `fa-graduation-cap`)
- Add title and description
- Repeat for up to 6 features

### **4. Add Statistics**

Go to **Statistics** tab:
- Click "Add Statistic"
- Enter label (e.g., "Active Students")
- Enter value (e.g., "1,200+")
- Repeat for up to 4 statistics

### **5. Configure Contact**

Go to **Contact & Social** tab:
- Fill in email, phone, WhatsApp, address
- Add social media URLs

### **6. Optimize for SEO**

Go to **SEO** tab:
- Add meta title (50-60 characters)
- Add meta description (150-160 characters)
- Add keywords (comma-separated)

---

## 🔧 **Technical Details**

### **Database Schema**

**New Fields in `schools` table:**

| Field | Type | Description |
|-------|------|-------------|
| `show_landing_page` | boolean | Enable/disable landing page |
| `hero_title` | string | Hero section title |
| `hero_subtitle` | string | Hero section subtitle |
| `hero_description` | text | Hero section description |
| `hero_image` | string | Path to hero background image |
| `hero_cta_text` | string | CTA button text |
| `hero_cta_link` | string | CTA button URL |
| `about_title` | text | About section title |
| `about_content` | text | About section content |
| `about_image` | string | Path to about section image |
| `features` | json | Array of features with icon, title, description |
| `statistics` | json | Array of statistics with label and value |
| `contact_address` | text | Contact address |
| `contact_phone` | string | Contact phone |
| `contact_email` | string | Contact email |
| `contact_whatsapp` | string | WhatsApp number |
| `social_facebook` | string | Facebook URL |
| `social_instagram` | string | Instagram URL |
| `social_twitter` | string | Twitter URL |
| `social_youtube` | string | YouTube URL |
| `meta_title` | string | SEO meta title |
| `meta_description` | text | SEO meta description |
| `meta_keywords` | text | SEO keywords |

### **Routes**

| Method | URI | Name | Action |
|--------|-----|------|--------|
| GET | `/admin/schools/{school}/landing-page` | `admin.landing-page.edit` | Show editor |
| PUT | `/admin/schools/{school}/landing-page` | `admin.landing-page.update` | Update landing page |
| GET | `/admin/schools/{school}/landing-page/preview` | `admin.landing-page.preview` | Preview landing page |

### **Model Methods**

**School Model:**

```php
// Get hero image URL with fallback
$school->hero_image_url

// Get about image URL
$school->about_image_url

// Get features with defaults
$school->features // Returns array

// Get statistics with defaults
$school->statistics // Returns array
```

### **Storage Paths**

- Hero images: `storage/app/public/landing-pages/heroes/`
- About images: `storage/app/public/landing-pages/about/`

---

## 🎨 **Design Features**

### **Responsive Design**
- Mobile-first approach
- Grid layouts adapt to screen size
- Touch-friendly buttons and navigation

### **Modern UI**
- Gradient hero sections
- Smooth transitions and hover effects
- Shadow and blur effects
- Font Awesome icons throughout

### **Theme Integration**
- Automatically applies school theme colors
- Respects theme settings (fonts, colors, etc.)
- Consistent with rest of the application

---

## 🧪 **Testing**

### **Test Landing Page**

1. **View as Guest:**
   - Visit `/` (root URL)
   - Should see custom landing page if enabled

2. **Test Preview:**
   - Edit landing page
   - Click "Preview" button
   - Opens in new tab

3. **Test Features:**
   - Add/remove features dynamically
   - Upload images (hero and about)
   - Enable/disable landing page

4. **Test Social Links:**
   - Click social media icons in footer
   - Should open in new tab

---

## 🚨 **Troubleshooting**

### **Landing Page Not Showing**

1. Check `show_landing_page` is `true` in database
2. Check if school has active theme
3. Clear cache: `php artisan optimize:clear`

### **Images Not Displaying**

1. Run: `php artisan storage:link`
2. Check file permissions on `storage/app/public`
3. Verify image paths in database

### **Features/Statistics Not Saving**

1. Check JSON format in database
2. Verify array structure matches expected format
3. Check browser console for JavaScript errors

---

## 📊 **Database Queries**

### **Get Active Landing Pages**

```php
School::where('show_landing_page', true)
      ->where('is_active', true)
      ->get();
```

### **Get School by Domain**

```php
$domain = request()->getHost();
$school = School::where('domain', $domain)->first();
```

---

## 🔐 **Security**

### **Access Control**
- Only admins can edit landing pages
- Protected by `auth` and `role:admin` middleware

### **Validation**
- Image uploads limited to 2MB
- Accepted formats: jpg, png, gif, webp
- URL validation for social media links
- XSS protection via Laravel's built-in escaping

### **File Storage**
- Images stored in protected storage
- Public access via symlink
- Automatic old image deletion on update

---

## 🎯 **Best Practices**

### **Images**
- **Hero Image:** 1920x1080px (Full HD)
- **About Image:** 600x400px
- Format: JPG or PNG
- Compress images before upload

### **Content**
- **Hero Title:** Keep under 60 characters
- **Hero Description:** 2-3 sentences max
- **Features:** Limit to 6 for best visual balance
- **Statistics:** Limit to 4 for even grid layout

### **SEO**
- **Meta Title:** 50-60 characters
- **Meta Description:** 150-160 characters
- **Keywords:** 5-10 relevant keywords

---

## 🚀 **Future Enhancements**

Potential features for future versions:

- [ ] Drag & drop section ordering
- [ ] Image gallery/carousel
- [ ] Testimonials section
- [ ] FAQ accordion
- [ ] Newsletter signup form
- [ ] Video background support
- [ ] Custom HTML/CSS injection
- [ ] A/B testing support
- [ ] Analytics integration
- [ ] Multi-language support

---

## 📞 **Support**

For questions or issues:

1. Check this documentation first
2. Review Laravel logs: `storage/logs/laravel.log`
3. Enable debug mode in `.env`: `APP_DEBUG=true`
4. Check browser console for JavaScript errors

---

## 📝 **Change Log**

**Version 1.0.0** (October 23, 2025)
- ✅ Initial release
- ✅ Full CRUD for landing pages
- ✅ 7-section tabbed editor
- ✅ Image upload support
- ✅ Dynamic features and statistics
- ✅ SEO optimization
- ✅ Social media integration
- ✅ Mobile responsive design
- ✅ Theme integration
- ✅ Preview functionality

---

## 🎉 **Quick Start Checklist**

- [x] Run migrations
- [x] Seed sample data (optional)
- [x] Create storage symlink: `php artisan storage:link`
- [x] Access admin panel: `/admin/schools`
- [x] Click paint brush icon on any school
- [x] Edit landing page content
- [x] Upload images
- [x] Preview changes
- [x] Save and publish
- [x] Visit `/` to see landing page

---

**🎨 Enjoy your beautiful, dynamic landing pages!**

