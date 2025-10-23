# 🚀 Token Access - Quick Start Guide

## ✅ **FEATURE IS READY!**

**Login via Token Ujian** is now fully implemented! Here's how to use it in 5 minutes! ⚡

---

## 🎯 **What You Get**

Peserta dapat mengikuti ujian **tanpa login/registrasi**! Perfect untuk:
- Ujian Tryout 🎓
- Placement Test 📝
- Ujian Publik 🌐

---

## ⚡ **Quick Demo (5 Minutes)**

### **Step 1: Create Exam with Token (2 min)**
```bash
1. Login: admin@example.com / password
2. Go to: /admin/exams/create
3. Fill exam details
4. Scroll to "Akses via Token (Guest Access)"
5. ✅ Check "Izinkan Akses via Token"
6. ✅ Check "Wajib Isi Nama"
7. Set "Maksimal Penggunaan Token" = 10
8. Click "Simpan Ujian"
```

**Result:** Token generated! (e.g., `ABC123XY`)

### **Step 2: View Token (1 min)**
```bash
1. Go to exam detail page
2. See token in big display:
   ┌───────────────────────┐
   │  Token: ABC123XY  [Copy] │
   │  Usage: 0 / 10 times    │
   └───────────────────────┘
3. Click "Copy" to copy token
```

### **Step 3: Test as Guest (2 min)**
```bash
1. Open incognito window
2. Go to: /exam
3. Enter token: ABC123XY
4. Fill name: "Test Guest"
5. Check agreement
6. Click "Mulai Ujian"
7. Answer questions
8. Submit exam
9. View results! ✅
```

**Result:** Exam completed! Usage counter: 1 / 10

---

## 📊 **Check Results**

### **As Admin:**
```bash
1. Go to: /admin/exams/{id}
2. Click "Lihat Hasil"
3. See guest attempts:
   - Name: Test Guest
   - Email: (empty if not required)
   - Score: 80%
   - Status: Completed
```

### **Database Query:**
```sql
SELECT 
    guest_name,
    guest_email,
    score,
    passed,
    submitted_at
FROM exam_attempts
WHERE exam_id = 1 
  AND is_guest = 1
ORDER BY submitted_at DESC;
```

---

## 🔧 **Configuration Options**

### **Token Settings:**
| Setting | Description | Example |
|---------|-------------|---------|
| `allow_token_access` | Enable guest access | ✅ true |
| `require_guest_name` | Require name | ✅ true |
| `require_guest_email` | Require email | ❌ false |
| `max_token_uses` | Limit uses | 50 or null (unlimited) |

### **Use Cases:**

**Tryout (Limited):**
```
allow_token_access = true
require_guest_name = true
require_guest_email = true  
max_token_uses = 100
```

**Public Quiz (Unlimited):**
```
allow_token_access = true
require_guest_name = false
require_guest_email = false
max_token_uses = null
```

**Placement Test:**
```
allow_token_access = true
require_guest_name = true
require_guest_email = true
max_token_uses = null
```

---

## 📱 **Share with Participants**

### **Via WhatsApp:**
```
🎓 Undangan Ujian Tryout

Silakan akses ujian melalui:
🌐 https://yoursite.com/exam

Token Akses: ABC123XY

📝 Durasi: 60 menit
📋 Jumlah Soal: 30 soal

Selamat mengerjakan! ✨
```

### **Via Email:**
```html
<h2>Undangan Ujian Tryout</h2>
<p>Anda diundang untuk mengikuti ujian tryout.</p>

<p><strong>Cara Akses:</strong></p>
<ol>
  <li>Kunjungi: <a href="https://yoursite.com/exam">https://yoursite.com/exam</a></li>
  <li>Masukkan token: <strong>ABC123XY</strong></li>
  <li>Isi nama lengkap Anda</li>
  <li>Klik "Mulai Ujian"</li>
</ol>

<p>Selamat mengerjakan! 📝</p>
```

### **Via Poster/Flyer:**
```
╔══════════════════════════╗
║  UJIAN TRYOUT GRATIS!   ║
║                          ║
║  Scan QR atau Akses:     ║
║  yoursite.com/exam       ║
║                          ║
║  Token: ABC123XY         ║
║                          ║
║  Durasi: 60 menit        ║
║  Soal: 30 soal           ║
╚══════════════════════════╝
```

---

## 🐛 **Troubleshooting**

### **"Token tidak valid"**
✅ Check:
- Token exactly 8 characters
- Exam is published
- Exam time is active

### **"Token sudah mencapai batas"**
✅ Solution:
- Increase `max_token_uses`
- Or generate new token

### **Guest can't submit**
✅ Check:
- Time not expired
- All required fields filled
- Internet connection stable

---

## 📚 **Full Documentation**

For complete details, see:
- 📄 `TOKEN-ACCESS-IMPLEMENTATION.md` - Full technical guide
- 📄 `README.md` - Main project documentation

---

## 🎯 **Quick Commands**

### **Check Token Usage:**
```bash
php artisan tinker
>>> $exam = \App\Models\Exam::find(1);
>>> echo "Token: " . $exam->access_token;
>>> echo "Used: " . $exam->current_token_uses . " / " . ($exam->max_token_uses ?? '∞');
```

### **Reset Token Usage:**
```bash
php artisan tinker
>>> $exam = \App\Models\Exam::find(1);
>>> $exam->resetTokenUses();
>>> echo "Token usage reset!";
```

### **Generate New Token:**
```bash
php artisan tinker
>>> $exam = \App\Models\Exam::find(1);
>>> $newToken = $exam->generateAccessToken();
>>> echo "New token: " . $newToken;
```

### **List Guest Attempts:**
```bash
php artisan tinker
>>> $attempts = \App\Models\ExamAttempt::where('is_guest', true)->get();
>>> foreach($attempts as $a) {
...     echo "{$a->guest_name}: {$a->score}%\n";
... }
```

---

## ✅ **That's It!**

**Token Access is now:**
- ✅ Fully working
- ✅ Easy to use
- ✅ Production ready
- ✅ Secure & tracked

**Time to first token:** 2 minutes  
**Time to first guest exam:** 5 minutes total  

**Happy Testing! 🎉**

---

**Quick Start Time:** ⏱️ 5 minutes  
**Difficulty:** ⭐ Super Easy  
**Status:** ✅ Ready to Use  

**Last Updated:** October 23, 2025

