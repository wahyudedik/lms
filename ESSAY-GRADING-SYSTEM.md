# 📝 Sistem Penilaian Essay 3-in-1

## 🎯 Overview

Sistem ini mengintegrasikan **3 mode penilaian essay** dalam satu platform:
1. **Manual Grading** - Guru review secara manual
2. **Keyword Matching** - Auto-grading berdasarkan kata kunci
3. **Similarity Matching** - Auto-grading berdasarkan similarity dengan jawaban model

Semua mode **bisa dikombinasikan** dengan manual override dari guru!

---

## 🗃️ Database Schema

### Migration: `add_essay_grading_fields_to_questions_table`

**Fields ditambahkan ke tabel `questions`:**

| Field | Type | Description |
|-------|------|-------------|
| `essay_grading_mode` | ENUM | Mode penilaian: `manual`, `keyword`, `similarity` |
| `essay_keywords` | JSON | Array kata kunci untuk keyword matching |
| `essay_keyword_points` | JSON | Array poin per kata kunci |
| `essay_model_answer` | TEXT | Jawaban model untuk similarity matching |
| `essay_min_similarity` | INTEGER | Minimal similarity % (0-100) |
| `essay_case_sensitive` | BOOLEAN | Apakah case-sensitive |

---

## 🔧 Backend Implementation

### 1. Question Model (`app/Models/Question.php`)

**New Methods:**

```php
// Auto-grade essay by keywords
public function gradeEssayByKeywords(string $userAnswer): float

// Auto-grade essay by similarity
public function gradeEssayBySimilarity(string $userAnswer): float

// Get essay grading mode display name
public function getEssayGradingModeDisplayAttribute(): string

// Check if needs manual grading
public function needsManualGrading(): bool

// Check if has auto-grading enabled
public function hasAutoGrading(): bool
```

**Keyword Matching Logic:**
- Cek apakah setiap kata kunci ada di jawaban siswa
- Case-insensitive (opsional)
- Akumulasi poin untuk setiap kata kunci yang ditemukan
- Max poin = total poin soal

**Similarity Matching Logic:**
- Gunakan PHP `similar_text()` function
- Calculate similarity percentage
- Penalty 50% jika di bawah minimal similarity threshold
- Round ke 2 desimal

---

### 2. Question Controllers

**Admin & Guru Controllers Updated:**
- Validasi conditional berdasarkan `essay_grading_mode`
- Simpan konfigurasi essay (keywords, model answer, dll)
- Clear fields yang tidak terpakai saat switch mode

**Validation Rules:**

```php
// Base
'essay_grading_mode' => 'required|in:manual,keyword,similarity',
'essay_case_sensitive' => 'nullable|boolean',

// For keyword mode
'essay_keywords' => 'required|array|min:1',
'essay_keywords.*' => 'required|string',
'essay_keyword_points' => 'required|array|min:1',
'essay_keyword_points.*' => 'required|numeric|min:0',

// For similarity mode
'essay_model_answer' => 'required|string',
'essay_min_similarity' => 'required|integer|min:0|max:100',
```

---

### 3. Guru Review Interface

**New Controller Method:**
```php
Guru\ExamController@reviewEssays(Exam $exam)
Guru\ExamController@gradeEssay(Request $request, Exam $exam, Answer $answer)
```

**New Route:**
```php
Route::get('exams/{exam}/review-essays', [GuruExamController::class, 'reviewEssays'])
    ->name('guru.exams.review-essays');
```

**Features:**
- Tab-based navigation per soal essay
- Display auto-grading results (jika ada)
- Form manual override nilai & feedback
- Badge "Perlu Review" untuk yang belum dinilai
- Auto-recalculate attempt score setelah grading

---

## 🎨 Frontend Implementation

### Create Question Form (`admin/questions/create.blade.php`)

**Essay Configuration Section:**

```html
<!-- Essay Grading Mode Selector -->
<select name="essay_grading_mode">
    <option value="manual">Manual (Guru Review)</option>
    <option value="keyword">Keyword Matching (Auto-Grade)</option>
    <option value="similarity">Similarity Matching (Auto-Grade)</option>
</select>

<!-- Case Sensitive Checkbox -->
<input type="checkbox" name="essay_case_sensitive">

<!-- Keyword Fields (dynamic) -->
<div id="keyword-fields">
    <input type="text" name="essay_keywords[]" placeholder="Kata kunci">
    <input type="number" name="essay_keyword_points[]" placeholder="Poin">
    <button type="button" class="remove-keyword">×</button>
</div>

<!-- Similarity Fields -->
<div id="similarity-fields">
    <textarea name="essay_model_answer" placeholder="Jawaban model..."></textarea>
    <input type="number" name="essay_min_similarity" value="70">
</div>
```

**JavaScript Handlers:**
- Toggle visibility berdasarkan `essay_grading_mode`
- Dynamic add/remove keyword fields
- Auto-initialize 3 keywords untuk mode keyword
- Form validation

---

### Review Essays View (`guru/exams/review-essays.blade.php`)

**Features:**
1. **Exam Info Card**
   - Total soal essay
   - Total attempts
   - Jumlah yang perlu review

2. **Tab Navigation**
   - Tab per soal essay
   - Badge dengan total poin

3. **Question Card**
   - Teks soal
   - Mode penilaian
   - Display keywords/model answer

4. **Student Answer Cards**
   - Info siswa & attempt
   - Jawaban siswa
   - Auto-grading result (jika ada)
   - Form manual grading
   - Badge status (Perlu Review / Sudah Dinilai)

---

## 📊 Sample Data (Seeder)

### ExamSeeder Updated

**3 Essay Questions per Exam:**

1. **Manual Mode (30 poin)**
   - Question: "Pentingnya pendidikan karakter..."
   - No auto-grading
   - Guru must review manually

2. **Keyword Mode (25 poin)**
   - Question: "Jelaskan proses fotosintesis..."
   - Keywords: fotosintesis (4), klorofil (4), cahaya matahari (4), karbon dioksida (4), oksigen (4), glukosa (5)
   - Total: 25 poin

3. **Similarity Mode (20 poin)**
   - Question: "Apa itu pemanasan global?"
   - Model Answer: 250+ kata tentang definisi, penyebab, dampak
   - Min Similarity: 70%

---

## 🧪 How to Test

### 1. Test Manual Mode

```bash
# Login as Guru
# Go to: /guru/exams/{exam}/questions/create

1. Select Type: Essay
2. Mode: Manual (Guru Review)
3. Question: "Jelaskan pendapat Anda tentang..."
4. Points: 30
5. Save
```

**Expected:**
- Siswa submit → nilai = 0
- Guru harus review manual di `/guru/exams/{exam}/review-essays`

---

### 2. Test Keyword Mode

```bash
# Create essay question with keywords

1. Type: Essay
2. Mode: Keyword Matching
3. Question: "Jelaskan fotosintesis!"
4. Keywords:
   - fotosintesis → 5 poin
   - klorofil → 5 poin
   - cahaya → 5 poin
   - oksigen → 5 poin
5. Total: 20 poin
6. Save
```

**Test Answer:**
```
"Fotosintesis adalah proses tumbuhan membuat makanan 
menggunakan cahaya matahari dengan bantuan klorofil. 
Hasilnya adalah oksigen dan glukosa."
```

**Expected Score:**
- Keyword found: fotosintesis ✓, klorofil ✓, cahaya ✓, oksigen ✓
- Auto-grade: 20/20 poin

---

### 3. Test Similarity Mode

```bash
# Create essay with model answer

1. Type: Essay
2. Mode: Similarity Matching
3. Question: "Apa itu AI?"
4. Model Answer: 
   "Artificial Intelligence (AI) adalah simulasi kecerdasan 
   manusia oleh mesin komputer. AI mampu belajar, 
   beradaptasi, dan membuat keputusan."
5. Min Similarity: 70%
6. Total: 15 poin
7. Save
```

**Test Answer (High Similarity ~85%):**
```
"AI atau kecerdasan buatan adalah kemampuan komputer untuk 
meniru kecerdasan manusia, seperti belajar dan mengambil 
keputusan."
```

**Expected:**
- Similarity: ~85%
- Auto-grade: 12.75/15 poin (85%)

**Test Answer (Low Similarity ~50%):**
```
"Komputer pintar yang bisa berpikir sendiri."
```

**Expected:**
- Similarity: ~50% (< 70% threshold)
- Auto-grade: 3.75/15 poin (50% × 0.5 penalty)

---

### 4. Test Manual Override

```bash
# After auto-grading

1. Go to: /guru/exams/{exam}/review-essays
2. See auto-grading result
3. Override with manual grade
4. Add feedback
5. Save
```

**Expected:**
- Nilai berubah sesuai input guru
- Attempt score recalculated
- Feedback tersimpan

---

## 📍 Routes

### Guru Routes

```php
// Review interface
GET /guru/exams/{exam}/review-essays

// Grade essay
POST /guru/exams/{exam}/answers/{answer}/grade
```

---

## 🎓 Cara Kerja Auto-Grading

### Keyword Matching

```php
function gradeEssayByKeywords(string $userAnswer): float {
    $totalPoints = 0;
    
    foreach ($this->essay_keywords as $index => $keyword) {
        if (str_contains(strtolower($answer), strtolower($keyword))) {
            $totalPoints += $this->essay_keyword_points[$index];
        }
    }
    
    return min($totalPoints, $this->points);
}
```

**Kelebihan:**
- ✅ Cepat & otomatis
- ✅ Fleksibel (siswa bisa pakai kalimat sendiri)
- ✅ Easy to configure

**Kekurangan:**
- ❌ Tidak cek struktur kalimat
- ❌ Bisa "curang" dengan list kata kunci

---

### Similarity Matching

```php
function gradeEssayBySimilarity(string $userAnswer): float {
    similar_text($this->essay_model_answer, $answer, $percent);
    
    $earnedPoints = ($percent / 100) * $this->points;
    
    // Penalty if below threshold
    if ($percent < $this->essay_min_similarity) {
        $earnedPoints *= 0.5;
    }
    
    return round($earnedPoints, 2);
}
```

**Kelebihan:**
- ✅ Lebih "pintar" dari keyword
- ✅ Deteksi jawaban yang mirip
- ✅ Toleran typo kecil

**Kekurangan:**
- ❌ Perlu jawaban model yang baik
- ❌ Tidak bisa nilai opini/argumen
- ❌ Bisa terlalu strict/lenient

---

## 🔐 Security & Validation

1. **Authorization**
   - Guru hanya bisa grade essay dari course yang dia ajar
   - Check `exam->course->instructor_id === auth()->id()`

2. **Validation**
   - Max points tidak melebihi question points
   - Conditional validation based on grading mode
   - Sanitize inputs

3. **Data Integrity**
   - Auto-recalculate attempt score after manual grading
   - Clear unused fields when changing mode
   - JSON validation for keywords & points

---

## 📈 Future Enhancements

1. **Advanced NLP**
   - Gunakan library NLP untuk lebih akurat
   - Sentiment analysis
   - Synonym detection

2. **Batch Grading**
   - Grade multiple answers at once
   - Bulk feedback

3. **Rubric System**
   - Define grading criteria
   - Point allocation per criterion

4. **AI Integration**
   - OpenAI API for intelligent grading
   - Automated feedback generation

5. **Analytics**
   - Common mistakes per question
   - Keyword hit rate
   - Average similarity scores

---

## 🐛 Troubleshooting

### Auto-grading tidak berfungsi

**Check:**
1. Apakah `essay_grading_mode` sudah di-set?
2. Apakah keywords/model_answer sudah diisi?
3. Apakah `calculatePoints()` dipanggil saat submit?

### Nilai tidak akurat

**Keyword Mode:**
- Check case-sensitivity setting
- Pastikan keyword tidak terlalu umum
- Adjust keyword points distribution

**Similarity Mode:**
- Review model answer quality
- Adjust min_similarity threshold
- Consider answer length differences

### Guru tidak bisa override

**Check:**
1. Apakah route grade-essay sudah didefinisikan?
2. Apakah authorization check passed?
3. Apakah form validation passed?

---

## 📝 Changelog

### Version 1.0.0 (2025-10-21)

**Added:**
- ✅ 3 essay grading modes (manual, keyword, similarity)
- ✅ Question model auto-grading methods
- ✅ Admin & Guru question creation forms
- ✅ Guru review interface
- ✅ Sample data seeder
- ✅ Documentation

**Features:**
- Auto-grading dengan keyword matching
- Auto-grading dengan similarity matching
- Manual grading dengan override capability
- Case-sensitive toggle
- Tab-based review interface
- Real-time attempt score recalculation

---

## 👥 Credits

**Developed by:** Cursor AI Assistant  
**Framework:** Laravel 12  
**Database:** MySQL  
**Frontend:** Blade + Tailwind CSS + Alpine.js  

---

## 📞 Support

Untuk pertanyaan atau bug report, silakan buat issue di repository atau hubungi tim developer.

**Happy Grading! 🎓**

