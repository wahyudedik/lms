# 📝 Exam/CBT System Guide

Complete guide untuk sistem ujian Computer-Based Test (CBT) di Laravel LMS.

## 📋 Overview

Exam System adalah fitur inti LMS yang memungkinkan:
- ✅ Create & manage exams
- ✅ Multiple question types
- ✅ Auto-grading
- ✅ Anti-cheat features
- ✅ Timer & scheduling
- ✅ Detailed reports
- ✅ Guest access dengan token
- ✅ Offline mode support

## 🎯 Exam Features

### Question Types

**1. Multiple Choice**
- Radio button selection
- Single correct answer
- A, B, C, D options
- Auto-graded

**2. True/False**
- Boolean questions
- Quick to answer
- Auto-graded

**3. Short Answer**
- Text input
- Exact match checking
- Case-insensitive
- Auto-graded

**4. Essay**
- Long-form answers
- Manual grading required
- Text area input
- Subjective evaluation

### Anti-Cheat Features

**Tab Switch Detection:**
- Detect when student switches tabs
- Configurable max switches
- Auto-submit on limit exceeded
- Track violations

**Fullscreen Mode:**
- Require fullscreen during exam
- Exit detection
- Warning messages
- Violation tracking

**Time Tracking:**
- Track total time spent
- Record start/submit times
- Timer countdown
- Auto-submit on timeout

**IP & User Agent:**
- Record IP address
- Track browser info
- Detect device changes
- Audit trail

## 🚀 Creating an Exam

### Step 1: Basic Information

```
Admin → Exams → Create New Exam
```

**Required Fields:**
- Course (select)
- Title
- Description
- Duration (minutes)
- Pass Score (%)

**Optional:**
- Instructions
- Start/End time
- Max attempts

### Step 2: Configure Settings

**Display Options:**
```
☑ Shuffle Questions
☑ Shuffle Options
☑ Show Results Immediately
☑ Show Correct Answers
```

**Security:**
```
☑ Require Fullscreen
☑ Detect Tab Switch (Max: 3)
```

**Guest Access:**
```
☑ Allow Token Access
☑ Require Name
☑ Require Email
```

**Offline Mode:**
```
☑ Enable Offline Mode
Cache Duration: 24 hours
```

### Step 3: Add Questions

```
Exams → [Your Exam] → Questions → Add Question
```

**Question Form:**
- Question Text
- Type (MC, T/F, Short, Essay)
- Points
- Correct Answer
- Options (for MC)
- Order

**Bulk Import:**
- Import from Question Bank
- Select multiple questions
- Auto-assign points
- Randomize order

### Step 4: Publish

```
Edit Exam → ☑ Publish Exam → Save
```

Exam now available to students!

## 📊 Question Management

### Question Bank

**Organize Questions:**
- By subject
- By difficulty
- By tags
- Reusable across exams

**Import/Export:**
- Excel format
- CSV format
- JSON format
- Bulk operations

### Question Order

**Drag & Drop:**
- Reorder questions
- Visual interface
- Save automatically

**Auto-Shuffle:**
- Random order per student
- Stored in attempt
- Consistent view

## 👥 Student Experience

### Taking an Exam

**1. Start Exam:**
```
Courses → [Course] → Exams → Take Exam
```

**2. Read Instructions:**
- Duration
- Number of questions
- Grading rules
- Anti-cheat warnings

**3. Answer Questions:**
- Navigate with buttons
- Auto-save answers
- Mark for review
- Timer visible

**4. Submit:**
- Review answers
- Confirm submission
- Get score (if enabled)

### Exam Interface

**Features:**
- Question counter (1/20)
- Timer countdown
- Progress indicator
- Navigation buttons
- Auto-save status
- Offline indicator

## 📈 Grading & Results

### Auto-Grading

**Graded Automatically:**
- Multiple Choice
- True/False
- Short Answer (exact match)

**Manual Grading:**
- Essay questions
- Partial credit
- Feedback comments

### Score Calculation

```
Score = (Correct Points / Total Points) × 100%
```

**Pass/Fail:**
```
if (score >= pass_score) {
    status = 'passed';
} else {
    status = 'failed';
}
```

### Results Display

**For Students:**
- Total score
- Pass/Fail status
- Time spent
- Correct/Wrong count
- Correct answers (if enabled)

**For Instructors:**
- All student info
- Question-by-question breakdown
- Statistics
- Export options

## 🔐 Guest Access (Token System)

### Enable Guest Access

```
Edit Exam → Guest Access Settings
☑ Allow Token Access
☑ Require Guest Name
☑ Require Guest Email
Max Uses: 100
```

### Generate Token

```
Exams → [Your Exam] → Generate Token
```

**Token Features:**
- Unique URL
- No login required
- Usage tracking
- Expiration support

### Share Token

```
Copy URL:
https://lms.test/guest/exams/{token}

Share via:
- Email
- WhatsApp
- QR Code
```

## 📊 Analytics & Reports

### Exam Statistics

**Overall:**
- Total attempts
- Average score
- Pass rate
- Time distribution

**Per Question:**
- Correct rate
- Most missed
- Time spent
- Difficulty level

### Export Reports

**Formats:**
- Excel (.xlsx)
- CSV
- PDF
- JSON

**Data Included:**
- Student info
- Scores
- Answers
- Time data
- Violations

## 🎓 Best Practices

### Creating Good Exams

**1. Clear Questions:**
- Unambiguous wording
- Proper grammar
- No tricks
- Fair difficulty

**2. Balanced Mix:**
- Vary question types
- Mix difficulties
- Cover all topics
- Appropriate length

**3. Reasonable Time:**
```
Estimate: Questions × 2 minutes + 10 minutes
Example: 20 questions = 50 minutes
```

**4. Set Pass Score:**
- 60% for basic
- 70% for standard
- 80% for advanced
- 90% for expert

### Security Settings

**Recommended:**
```
☑ Require Fullscreen
☑ Detect Tab Switch (Max: 3)
☑ Random Question Order
☑ Random Option Order
```

**High-Security:**
```
☑ All above +
☑ Hide Correct Answers
☑ Single Attempt Only
☑ Time Window (specific dates)
☑ Disable Copy/Paste
```

## 🐛 Troubleshooting

### Exam Not Showing

**Check:**
- Exam published?
- Course enrolled?
- Time window active?
- Max attempts not reached?

### Timer Issues

**Solutions:**
- Check server time
- Verify duration setting
- Test in different browser
- Clear cache

### Auto-Submit Not Working

**Fix:**
- Ensure JavaScript enabled
- Check network connection
- Verify auto-submit setting
- Review browser console

### Questions Not Loading

**Debug:**
- Check question count
- Verify question status
- Test database connection
- Clear view cache

## 🔧 Advanced Features

### Exam Templates

**Create Reusable Templates:**
- Standard settings
- Common configurations
- Quick setup
- Consistency

### Question Pools

**Random Selection:**
- Define question pool
- Random picks per attempt
- Different exam per student
- Same difficulty level

### Adaptive Testing

**Coming Soon:**
- Difficulty adjustment
- Based on performance
- Shorter exams
- Better assessment

## 📱 Mobile Support

**Responsive Design:**
- Works on phones
- Tablet optimized
- Touch-friendly
- Same features

**Offline Mode:**
- Download on mobile
- Take exam offline
- Auto-sync when online
- Perfect for field tests

## 🎯 Tips for Students

### Before Exam

- Read instructions carefully
- Check system requirements
- Test internet connection
- Prepare study materials
- Rest well

### During Exam

- Manage time wisely
- Answer easy questions first
- Review before submit
- Don't panic if offline
- Stay in fullscreen

### After Exam

- Review results (if available)
- Note mistakes
- Ask instructor questions
- Learn from feedback
- Prepare for next time

## 📞 Support

**Need Help?**
- Check this guide
- Contact instructor
- Email support
- Check FAQ

**Report Issues:**
- Technical problems
- Grading disputes
- Access issues
- Feedback

---

**Exam System - Complete Guide! 📝✨**

Comprehensive CBT system with auto-grading, anti-cheat, dan advanced features!

