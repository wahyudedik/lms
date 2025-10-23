# 📊 Question Bank Import/Export Feature

## ✅ **IMPLEMENTASI COMPLETE!**

Fitur Import/Export untuk Question Bank telah berhasil diimplementasikan dengan lengkap!

---

## 🎯 **Features Implemented**

### 1. **Export Questions** 📤
- Export semua soal di bank ke Excel (.xlsx)
- Support filtering sebelum export (category, type, difficulty, status)
- File naming dengan timestamp
- Format Excel professional dengan styling

### 2. **Import Questions** 📥
- Import soal dari Excel/CSV
- Validasi data otomatis
- Auto-create categories jika belum ada
- Detailed import statistics (imported, skipped, errors)
- Error handling yang comprehensive

### 3. **Download Template** 📝
- Template CSV dengan format yang benar
- Include sample data sebagai contoh
- Dokumentasi field yang jelas

---

## 🚀 **How to Use**

### **A. Export Questions**

1. Go to: `/admin/question-bank`
2. **(Optional)** Apply filters untuk export selektif:
   - Category
   - Type (MCQ Single, MCQ Multiple, Matching, Essay)
   - Difficulty (Easy, Medium, Hard)
   - Status (Active/Inactive)
   - Verification (Verified/Unverified)
3. Click button **"Export"** di header
4. File Excel akan ter-download otomatis

**Export File Contains:**
```
- ID
- Category
- Type
- Difficulty
- Question Text
- Options (JSON)
- Correct Answer (JSON)
- Points
- Explanation
- Tags
- Image URL
- Is Active
- Is Verified
- Times Used
- Created By
- Created At & Updated At
```

---

### **B. Import Questions**

#### **Step 1: Download Template**

1. Click button **"Import"** di header
2. Click **"Download Import Template"**
3. Template CSV akan ter-download dengan sample data

#### **Step 2: Prepare Your Data**

Open template dan isi data sesuai format:

**Required Fields:**
- `type`: mcq_single, mcq_multiple, matching, essay
- `difficulty`: easy, medium, hard
- `question_text`: Text soal

**Optional Fields:**
- `category`: Nama kategori (akan dibuat otomatis jika belum ada)
- `options_json`: JSON array untuk pilihan (MCQ)
- `correct_answer_json`: JSON array untuk jawaban benar
- `default_points`: Poin default (default: 1)
- `explanation`: Penjelasan jawaban
- `tags`: Tags dipisah koma (e.g. "math, algebra")
- `image_url`: URL gambar soal
- `is_active`: yes/no (default: yes)
- `is_verified`: yes/no (default: no)

**Example Data:**

```csv
type,difficulty,question_text,category,options_json,correct_answer_json,default_points,explanation,tags,image_url,is_active,is_verified
mcq_single,easy,What is 2+2?,Mathematics,"[""1"",""2"",""3"",""4""]","[""4""]",1,Simple addition,"math, basic",,yes,yes
mcq_multiple,medium,Select prime numbers,Mathematics,"[""1"",""2"",""3"",""4"",""5""]","[""2"",""3"",""5""]",2,Prime numbers are divisible by 1 and themselves,"math, prime",,yes,no
essay,hard,Explain photosynthesis,Biology,,,5,Process by which plants make food,"biology, science",,yes,yes
```

#### **Step 3: Upload File**

1. Click button **"Import"**
2. Click **"Select File to Import"**
3. Choose your Excel/CSV file
4. Click **"Import Questions"**

#### **Step 4: Review Results**

After import, you'll see:
- ✅ **Success**: Number of questions imported
- ⚠️ **Skipped**: Questions that couldn't be imported
- ❌ **Errors**: Detailed error messages for troubleshooting

---

## 📋 **Import Format Details**

### **Type Values:**
- `mcq_single` - Multiple Choice (Single Answer)
- `mcq_multiple` - Multiple Choice (Multiple Answers)
- `matching` - Matching Type
- `essay` - Essay/Text Type

### **Difficulty Values:**
- `easy` - Easy
- `medium` - Medium
- `hard` - Hard

### **JSON Format Examples:**

**Options JSON (for MCQ):**
```json
["Option 1", "Option 2", "Option 3", "Option 4"]
```

**Correct Answer JSON (Single):**
```json
["Option 2"]
```

**Correct Answer JSON (Multiple):**
```json
["Option 1", "Option 3"]
```

**Matching Pairs JSON:**
```json
[
  {"left": "Java", "right": "Programming Language"},
  {"left": "MySQL", "right": "Database"}
]
```

---

## 🎨 **UI Components**

### **Header Buttons:**
```
┌─────────────────────────────────────────────────┐
│ Question Bank                                    │
│                                                  │
│ [Statistics] [Import] [Export] [Add Question]   │
└─────────────────────────────────────────────────┘
```

### **Import Modal Features:**
- ✅ File upload with drag-and-drop support
- ✅ File preview (name, size)
- ✅ Import guidelines & instructions
- ✅ Download template button
- ✅ File type validation (.xlsx, .xls, .csv)
- ✅ Size limit: 10MB

---

## ⚙️ **Technical Implementation**

### **Files Created:**

1. **Export Class:**
   ```
   app/Exports/QuestionBankExport.php
   ```
   - Implements: FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
   - Supports filtering
   - Professional Excel styling

2. **Import Class:**
   ```
   app/Imports/QuestionBankImport.php
   ```
   - Implements: ToCollection, WithHeadingRow
   - Row-by-row validation
   - Error tracking & statistics
   - Auto-create categories

3. **Controller Methods:**
   ```php
   QuestionBankController::export()       // Export to Excel
   QuestionBankController::import()       // Import from Excel/CSV
   QuestionBankController::downloadTemplate()  // Download template
   ```

4. **Routes Added:**
   ```php
   GET  /admin/question-bank/export
   POST /admin/question-bank/import
   GET  /admin/question-bank/download-template
   ```

5. **View Updates:**
   ```
   resources/views/admin/question-bank/index.blade.php
   ```
   - Import/Export buttons
   - Import modal with form
   - JavaScript for modal handling

---

## 🔧 **Validation Rules**

### **Import Validation:**
```php
- type: required, in:mcq_single,mcq_multiple,matching,essay
- difficulty: required, in:easy,medium,hard
- question_text: required, string
- default_points: nullable, numeric, min:0
```

### **File Validation:**
```php
- file: required
- mimes: xlsx, xls, csv
- max: 10240 (10MB)
```

---

## 📊 **Import Statistics**

After each import, you'll receive:

```
✅ Success Message:
"50 questions imported successfully. 2 questions were skipped."

❌ Error Message (if any):
"Row 5: Type must be one of: mcq_single, mcq_multiple, matching, essay"
"Row 12: Question text is required"
```

---

## 🎯 **Use Cases**

### **1. Bulk Question Creation**
- Prepare soal di Excel
- Import sekaligus ratusan soal
- Save time vs manual entry

### **2. Question Migration**
- Export dari bank soal lama
- Edit/update di Excel
- Import ke bank soal baru

### **3. Question Sharing**
- Export soal berkualitas
- Share dengan tim lain
- Import ke sistem mereka

### **4. Backup & Restore**
- Export regular sebagai backup
- Restore kapan saja via import

---

## 🚀 **Testing Steps**

### **Test Export:**
```bash
1. Visit: http://lms.test/admin/question-bank
2. (Optional) Apply filters
3. Click "Export" button
4. Verify Excel file downloaded
5. Open file and check data format
```

### **Test Download Template:**
```bash
1. Click "Import" button
2. Click "Download Import Template"
3. Verify CSV downloaded
4. Check sample data format
```

### **Test Import:**
```bash
1. Download template
2. Fill with test data (3-5 questions)
3. Save as Excel or CSV
4. Click "Import" button
5. Upload file
6. Click "Import Questions"
7. Verify success message
8. Check imported questions in list
```

---

## 🐛 **Troubleshooting**

### **Import Errors:**

**"Invalid type value"**
- ✅ Use: mcq_single, mcq_multiple, matching, essay
- ❌ Don't use: MCQ, multiple choice, etc

**"Invalid JSON format"**
- ✅ Use: ["Option 1","Option 2"]
- ❌ Don't use: [Option 1, Option 2]

**"Category not found"**
- ✅ Categories are auto-created
- Check spelling and consistency

**"File too large"**
- Max file size: 10MB
- Split into multiple files if needed

---

## 📖 **Best Practices**

1. **Always download template first** untuk reference format
2. **Use consistent category names** untuk menghindari duplikasi
3. **Validate JSON** sebelum import (use JSON validator online)
4. **Test with small batch first** (5-10 questions)
5. **Review imported questions** untuk memastikan data correct
6. **Backup before large imports** (export existing data first)

---

## ✅ **Feature Complete Checklist**

- [x] Export functionality
- [x] Import functionality
- [x] Download template
- [x] File validation
- [x] Data validation
- [x] Error handling
- [x] Success/error messages
- [x] Import statistics
- [x] Auto-create categories
- [x] Professional Excel styling
- [x] Modal UI
- [x] Routes configured
- [x] Controller methods
- [x] Documentation

---

## 🎉 **Ready to Use!**

Fitur Import/Export Bank Soal sudah 100% siap digunakan!

**Test Now:**
```
http://lms.test/admin/question-bank
```

**Need Help?**
Check this documentation or contact the development team.

---

**Last Updated:** October 23, 2025
**Version:** 1.0.0
**Status:** ✅ Production Ready

