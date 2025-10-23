# ⚡ Quick Test Checklist (10 Minutes)

**Start:** `php artisan serve` or access `https://lms.test`

---

## 🔑 Test Accounts
```
Admin:  admin@lms.test / password
Guru:   guru@lms.test / password  
Siswa:  siswa@lms.test / password
```

---

## ✅ Quick Tests

### 1️⃣ **User Management** (2 min)
- [ ] Login as Admin → `/admin/users`
- [ ] Import users (download template, fill, upload)
- [ ] Export users (verify Excel with passwords)

### 2️⃣ **Courses** (2 min)
- [ ] Create course as Guru
- [ ] Enroll student manually
- [ ] Test code enrollment (as Siswa)

### 3️⃣ **Materials** (1 min)
- [ ] Add YouTube material
- [ ] View as Siswa
- [ ] Post comment + reply

### 4️⃣ **Exams** (5 min)
- [ ] Create exam with 4 question types:
  - MCQ Single
  - MCQ Multiple
  - Matching
  - Essay (keyword mode)
- [ ] Take exam as Siswa
- [ ] Verify auto-grading works

### 5️⃣ **Reports** (2 min)
- [ ] Export grades to Excel
- [ ] Export grades to PDF
- [ ] Generate student transcript

### 6️⃣ **Notifications** (1 min)
- [ ] Check notification bell
- [ ] Click notification
- [ ] Mark all as read

### 7️⃣ **Settings** (2 min)
- [ ] Update app settings
- [ ] Upload logo
- [ ] Create database backup
- [ ] Download backup

---

## ✅ All Tests Passed?

**YES:** 🎉 Ready for production!  
**NO:** Check `TESTING-GUIDE.md` for detailed troubleshooting

---

**Total Time:** ~10-15 minutes  
**Expected Result:** All features working perfectly ✨

