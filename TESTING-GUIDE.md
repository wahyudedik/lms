# 🧪 Laravel LMS - Complete Testing Guide

**Status:** Ready for Testing  
**Estimated Time:** 10-15 minutes (quick test) | 60 minutes (comprehensive)

---

## 🚀 Quick Test Checklist (10-15 Minutes)

### **Prerequisites**
```bash
# Ensure server is running
php artisan serve

# Or if using Herd/Valet
# Just access: https://lms.test
```

### **Test Accounts**
```
Admin:
- Email: admin@lms.test
- Password: password

Guru:
- Email: guru@lms.test
- Password: password

Siswa:
- Email: siswa@lms.test
- Password: password
```

---

## ✅ Module 1: User Management (2 minutes)

### **As Admin:**
```
1. Login: https://lms.test/login
   ✓ Email: admin@lms.test
   ✓ Password: password

2. Navigate to "User Management"
   ✓ Click "User Management" in navigation
   ✓ URL: /admin/users

3. Test User List
   ✓ Verify users are displayed
   ✓ Check pagination works
   ✓ Test filter by role (Admin/Guru/Siswa)

4. Test User Import
   ✓ Click "Import Users"
   ✓ Download template Excel
   ✓ Fill template with sample data:
     | Name        | Email              | Role   |
     |-------------|--------------------|--------|
     | Test Guru   | testguru@test.com  | guru   |
     | Test Siswa  | testsiswa@test.com | siswa  |
   ✓ Upload filled Excel
   ✓ Verify users imported successfully

5. Test User Export
   ✓ Click "Export Users"
   ✓ Excel file downloads
   ✓ Open Excel → verify data + plain_password column

6. Test Create User (Optional)
   ✓ Click "Create New User"
   ✓ Fill form (name, email, role)
   ✓ Submit
   ✓ Verify user created

Expected Result: ✅ All user operations work correctly
```

---

## ✅ Module 2: Course Management (2 minutes)

### **As Admin or Guru:**
```
1. Navigate to "Course Management" (Admin) or "My Courses" (Guru)
   ✓ URL: /admin/courses or /guru/courses

2. Create New Course
   ✓ Click "Create New Course"
   ✓ Fill form:
     - Title: "Matematika Dasar"
     - Description: "Kursus matematika untuk pemula"
     - Max Students: 30
     - Status: Published
   ✓ Submit
   ✓ Verify course created with auto-generated code

3. Test Enrollment
   ✓ Click course → "Manage Enrollments"
   ✓ Add a student manually (dropdown)
   ✓ Verify student enrolled

4. Test Course Code Enrollment (As Siswa)
   ✓ Logout as Admin
   ✓ Login as Siswa
   ✓ Go to "Browse Courses"
   ✓ Find course code (e.g., MAT001)
   ✓ Click "Enroll by Code"
   ✓ Enter code → Submit
   ✓ Verify enrolled successfully

Expected Result: ✅ Course creation and enrollment work
```

---

## ✅ Module 3: Learning Materials (1 minute)

### **As Guru:**
```
1. Navigate to "My Courses"
   ✓ Click a course → "Manage Materials"
   ✓ URL: /guru/courses/{course}/materials

2. Create Material
   ✓ Click "Add Material"
   ✓ Fill form:
     - Title: "Pengenalan Aljabar"
     - Type: YouTube
     - URL: https://www.youtube.com/watch?v=dQw4w9WgXcQ
     - Status: Published
   ✓ Submit
   ✓ Verify material created

3. Test Material View (As Siswa)
   ✓ Login as Siswa
   ✓ Go to "My Courses"
   ✓ Click enrolled course
   ✓ Verify material displayed
   ✓ Click material → YouTube video embeds

4. Test Comments
   ✓ Scroll to comments section
   ✓ Post a comment: "Bagus sekali!"
   ✓ Verify comment appears
   ✓ Reply to comment
   ✓ Verify reply works

Expected Result: ✅ Materials and comments work
```

---

## ✅ Module 4: CBT / Exam System (5 minutes)

### **As Guru:**
```
1. Navigate to "My Exams"
   ✓ Click "My Exams" → "Create Exam"
   ✓ Fill form:
     - Title: "Ujian Matematika"
     - Course: Select created course
     - Duration: 30 minutes
     - Pass Score: 70%
     - Status: Published
   ✓ Submit
   ✓ Verify exam created

2. Add Questions
   ✓ Click exam → "Manage Questions"
   ✓ Click "Add Question"

   **Question 1: MCQ Single**
   ✓ Type: MCQ Single Answer
   ✓ Question: "Berapa hasil 2 + 2?"
   ✓ Options:
     - 3 (wrong)
     - 4 (correct) ← select this
     - 5 (wrong)
   ✓ Points: 10
   ✓ Submit

   **Question 2: MCQ Multiple**
   ✓ Type: MCQ Multiple Answers
   ✓ Question: "Pilih bilangan genap:"
   ✓ Options:
     - 2 (correct) ← check
     - 3 (wrong)
     - 4 (correct) ← check
     - 5 (wrong)
   ✓ Points: 20
   ✓ Submit

   **Question 3: Matching**
   ✓ Type: Matching
   ✓ Question: "Cocokkan operasi dengan hasil:"
   ✓ Pairs:
     - 2 + 2 → 4
     - 3 x 3 → 9
   ✓ Points: 15
   ✓ Submit

   **Question 4: Essay (Auto-graded)**
   ✓ Type: Essay
   ✓ Question: "Jelaskan konsep aljabar"
   ✓ Grading Mode: Keyword Matching
   ✓ Keywords:
     - variabel: 5 points
     - persamaan: 5 points
     - matematika: 5 points
   ✓ Points: 15
   ✓ Submit

3. Test Exam Taking (As Siswa)
   ✓ Login as Siswa
   ✓ Go to "Exams" → Find created exam
   ✓ Click "Lihat Detail" → "Mulai Ujian"
   ✓ Answer all questions:
     - Q1: Select "4"
     - Q2: Check "2" and "4"
     - Q3: Match correctly
     - Q4: Type "Aljabar adalah cabang matematika yang menggunakan variabel dalam persamaan"
   ✓ Click "Submit Exam"
   ✓ Verify: Score calculated automatically
   ✓ Check: MCQ, Matching, Essay (keyword) auto-graded

4. Test Review
   ✓ Click "Review Hasil"
   ✓ Verify: All answers shown
   ✓ Verify: Correct/incorrect marked
   ✓ Verify: Score displayed
   ✓ Check: Passed/Failed status

Expected Result: ✅ All question types and grading work
```

---

## ✅ Module 5: Grades & Reports (2 minutes)

### **As Guru:**
```
1. Navigate to "Reports"
   ✓ Click "Reports" in navigation
   ✓ URL: /guru/reports

2. Select Course & View Reports
   ✓ Select course from dropdown
   ✓ Click "Filter"
   ✓ Verify: Exam list displayed with statistics

3. Test Excel Export
   ✓ Click "Export Excel" on an exam
   ✓ Verify: .xlsx file downloads
   ✓ Open file → check data:
     - Student names
     - Scores
     - Pass/Fail status
     - Timestamps

4. Test PDF Export
   ✓ Click "Export PDF" on an exam
   ✓ Verify: .pdf file downloads
   ✓ Open PDF → check:
     - Professional layout
     - Header with exam info
     - Statistics summary
     - Student list with scores

5. Test Student Transcript
   ✓ Scroll to "Export Transkrip Siswa"
   ✓ Select a student
   ✓ Click "Export Transkrip PDF"
   ✓ Verify: PDF downloads with all exam results

Expected Result: ✅ All export features work
```

### **As Siswa:**
```
1. Navigate to "My Transcript"
   ✓ Click "My Transcript" in navigation
   ✓ URL: /siswa/reports/my-transcript

2. View Transcript
   ✓ Select course
   ✓ Click "Lihat Transkrip"
   ✓ Verify: Statistics cards display correctly
   ✓ Check: All exam results shown

3. Test PDF Export
   ✓ Click "Export PDF"
   ✓ Verify: Personal transcript downloads
   ✓ Open PDF → check all data present

Expected Result: ✅ Student transcript works
```

---

## ✅ Module 6: Notifications (1 minute)

### **Test Notification Bell:**
```
1. As any logged-in user
   ✓ Check notification bell icon (top right)
   ✓ Verify: Unread count badge shows (if any)
   ✓ Click bell → dropdown opens
   ✓ Verify: Recent notifications listed

2. Test Notification Actions
   ✓ Click a notification
   ✓ Verify: Navigates to related page
   ✓ Verify: Notification marked as read

3. Test Mark All as Read
   ✓ Click "Tandai Semua Dibaca"
   ✓ Verify: All marked as read
   ✓ Badge disappears

4. Test Notifications Page
   ✓ Click "Lihat Semua Notifikasi"
   ✓ URL: /notifications
   ✓ Verify: Full list displayed
   ✓ Test delete notification
   ✓ Verify: Notification removed

Expected Result: ✅ Notifications work in real-time
```

---

## ✅ Module 7: Settings & Admin (2 minutes)

### **As Admin:**
```
1. Navigate to Settings
   ✓ Click "Settings" in navigation
   ✓ URL: /admin/settings

2. Update General Settings
   ✓ Change "App Name" to "My School LMS"
   ✓ Update school info (name, address, phone)
   ✓ Click "Simpan Pengaturan"
   ✓ Verify: Settings saved successfully
   ✓ Refresh page → changes persist

3. Upload Logo
   ✓ Scroll to "Tampilan"
   ✓ Upload logo image (JPG/PNG, < 2MB)
   ✓ Submit
   ✓ Verify: Logo preview appears
   ✓ Check: Logo displayed in app (if implemented)

4. Change Colors
   ✓ Click color picker for "Primary Color"
   ✓ Select a color (e.g., #FF0000 red)
   ✓ Submit
   ✓ Verify: Color saved

5. Toggle System Settings
   ✓ Toggle "Enable Registration"
   ✓ Toggle "Maintenance Mode"
   ✓ Submit
   ✓ Verify: Toggles saved

6. Test Database Backup
   ✓ Click "Backup Database"
   ✓ URL: /admin/settings/backup
   ✓ Click "Buat Backup Sekarang"
   ✓ Wait 5-10 seconds
   ✓ Verify: Backup file appears in list
   ✓ Check: File size and date displayed

7. Download Backup
   ✓ Click "Download" on backup
   ✓ Verify: .sql file downloads
   ✓ Check: File is valid SQL (open in text editor)

8. Delete Backup
   ✓ Click "Hapus" on backup
   ✓ Confirm deletion
   ✓ Verify: Backup removed from list

Expected Result: ✅ All settings and backup features work
```

---

## 🎯 Complete Feature Matrix

| Feature | Test Status | Notes |
|---------|-------------|-------|
| **User Management** |
| Login/Logout | [ ] | Test all roles |
| User CRUD | [ ] | Create, edit, delete |
| User Import | [ ] | Excel import |
| User Export | [ ] | Excel export with passwords |
| Role Management | [ ] | Admin, Guru, Siswa |
| Profile Edit | [ ] | Update info + photo |
| **Course Management** |
| Course CRUD | [ ] | Create, edit, delete |
| Course Code | [ ] | Auto-generated |
| Enrollment (Manual) | [ ] | Admin/Guru enroll |
| Enrollment (Code) | [ ] | Siswa self-enroll |
| Enrollment (Button) | [ ] | Quick enroll |
| **Materials** |
| Upload Files | [ ] | PDF, PPT, video |
| YouTube Embed | [ ] | Video integration |
| Comments | [ ] | Post & reply |
| Material Ordering | [ ] | Drag & drop |
| **CBT / Exams** |
| MCQ Single | [ ] | Auto-graded |
| MCQ Multiple | [ ] | Auto-graded |
| Matching | [ ] | Auto-graded |
| Essay (Manual) | [ ] | Guru grades |
| Essay (Keyword) | [ ] | Auto-graded |
| Essay (Similarity) | [ ] | Auto-graded |
| Timer | [ ] | Auto-submit |
| Anti-cheat | [ ] | Fullscreen, tab detect |
| Exam Results | [ ] | Score display |
| Review | [ ] | Show correct answers |
| **Reports** |
| Grades Dashboard | [ ] | Guru view |
| Excel Export | [ ] | Exam grades |
| PDF Export | [ ] | Exam grades |
| Student Transcript | [ ] | PDF per student |
| My Transcript | [ ] | Siswa view |
| **Notifications** |
| Material Published | [ ] | To enrolled students |
| Exam Scheduled | [ ] | To enrolled students |
| Exam Graded | [ ] | To exam taker |
| Notification Bell | [ ] | Real-time updates |
| Mark as Read | [ ] | Individual |
| Mark All Read | [ ] | Bulk action |
| **Settings** |
| General Settings | [ ] | App info |
| School Settings | [ ] | School info |
| Logo Upload | [ ] | Branding |
| Color Customization | [ ] | Theme |
| System Toggles | [ ] | Features on/off |
| Database Backup | [ ] | Create backup |
| Backup Download | [ ] | Get SQL file |
| Backup Delete | [ ] | Remove old backups |

---

## 🐛 Common Issues & Solutions

### **Issue 1: Routes not found (404)**
**Solution:**
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### **Issue 2: Logo upload fails**
**Solution:**
```bash
php artisan storage:link
# Check storage/app/public exists
# Check file permissions (755)
```

### **Issue 3: Database backup fails**
**Solution:**
- Check mysqldump path in SettingsController
- Ensure storage/app/backups directory exists
- Fallback method will trigger automatically

### **Issue 4: Notifications not showing**
**Solution:**
- Check database migrations ran
- Verify notification triggers in controllers
- Check browser console for JS errors

### **Issue 5: Excel import/export fails**
**Solution:**
```bash
composer require maatwebsite/excel
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
```

---

## 📋 Production Deployment Checklist

### **Pre-Deployment:**
- [ ] Update `.env` with production database
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_URL=https://yourdomain.com`
- [ ] Configure mail settings (SMTP)
- [ ] Generate new `APP_KEY` (`php artisan key:generate`)

### **Database:**
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed settings: `php artisan db:seed --class=SettingSeeder`
- [ ] Create admin account manually in DB
- [ ] Test database connection

### **File System:**
- [ ] Run `php artisan storage:link`
- [ ] Set proper permissions:
  - `chmod 755 storage` (recursively)
  - `chmod 755 bootstrap/cache` (recursively)
- [ ] Create `storage/app/backups` directory

### **Optimization:**
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Run `composer install --optimize-autoloader --no-dev`

### **Security:**
- [ ] Set up SSL certificate (Let's Encrypt)
- [ ] Configure CORS if needed
- [ ] Set up firewall rules
- [ ] Enable CSRF protection (enabled by default)
- [ ] Review file upload limits

### **Monitoring:**
- [ ] Set up error logging (Sentry, Bugsnag)
- [ ] Configure log rotation
- [ ] Set up uptime monitoring
- [ ] Configure backup cron job

### **Post-Deployment:**
- [ ] Test all critical features
- [ ] Import initial users
- [ ] Create sample courses
- [ ] Test notification system
- [ ] Monitor error logs for 24 hours

---

## 🎉 Final Checklist

Once all tests pass:
- [ ] ✅ All 8 modules tested and working
- [ ] ✅ No critical errors in logs
- [ ] ✅ Performance acceptable (page load < 2s)
- [ ] ✅ Mobile responsive checked
- [ ] ✅ Cross-browser tested (Chrome, Firefox, Safari)
- [ ] ✅ Security reviewed
- [ ] ✅ Documentation complete
- [ ] ✅ Backup system tested
- [ ] ✅ Ready for production! 🚀

---

**Happy Testing! 🧪✨**

If you encounter any issues, check documentation files for detailed guides.

