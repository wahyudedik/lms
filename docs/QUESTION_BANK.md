# 💾 Question Bank Guide

Complete guide untuk Question Bank system di Laravel LMS.

## 📋 Overview

Question Bank memungkinkan:
- ✅ Store reusable questions
- ✅ Organize by topics/tags
- ✅ Import/Export questions
- ✅ Quick exam creation
- ✅ Version control
- ✅ Difficulty levels
- ✅ Statistics tracking

## 🎯 Question Bank Features

### Organization

**Categorization:**
```
Question Bank
├── Subject/Category
│   ├── Mathematics
│   ├── Science
│   └── Programming
├── Difficulty Level
│   ├── Easy
│   ├── Medium
│   └── Hard
├── Question Type
│   ├── Multiple Choice
│   ├── True/False
│   ├── Short Answer
│   └── Essay
└── Tags
    ├── #laravel
    ├── #php
    └── #database
```

### Question Metadata

**Information Stored:**
- Question text
- Type
- Difficulty
- Points
- Correct answer
- Options (for MC)
- Tags
- Usage count
- Success rate
- Created date
- Last modified

## 🚀 Managing Questions

### Adding Questions

**Single Question:**
```
Admin/Guru → Question Bank → Add Question
```

**Form Fields:**
- Question text *
- Type *
- Category
- Difficulty
- Points
- Options (for MC)
- Correct answer *
- Explanation
- Tags

**Rich Text Editor:**
- Text formatting
- Images
- Code blocks
- Math equations
- Tables

### Bulk Import

**Excel Format:**
```excel
| Question | Type | Difficulty | Answer | Option_A | Option_B | ... |
|----------|------|------------|--------|----------|----------|-----|
| What is..| MC   | Easy       | A      | Answer1  | Answer2  | ... |
```

**CSV Format:**
```csv
question,type,difficulty,answer,option_a,option_b,option_c,option_d
"What is...","multiple_choice","easy","A","Ans1","Ans2","Ans3","Ans4"
```

**Steps:**
1. Download template
2. Fill in questions
3. Upload file
4. Review preview
5. Import questions

**Validation:**
- Required fields
- Valid types
- Correct format
- Duplicate detection

### Question Types

**1. Multiple Choice:**
```
Question: What is 2 + 2?
A) 3
B) 4 ✓ (Correct)
C) 5
D) 6
```

**2. True/False:**
```
Question: Laravel is a PHP framework.
Answer: True ✓
```

**3. Short Answer:**
```
Question: Capital of France?
Answer: Paris
(Case-insensitive matching)
```

**4. Essay:**
```
Question: Explain MVC architecture.
Answer: (Free text, manual grading)
```

## 📊 Question Organization

### Categories

**Subject-Based:**
- Mathematics
- Science
- Language
- History
- Programming
- Business

**Create Category:**
```
Question Bank → Categories → Add
- Name
- Description
- Parent category (optional)
- Icon/Color
```

### Tags

**Purpose:**
- Fine-grained organization
- Cross-category grouping
- Easy searching
- Trend tracking

**Examples:**
- `#beginner`
- `#advanced`
- `#practical`
- `#theory`
- `#important`

### Difficulty Levels

**Standard Levels:**
- **Easy** (60-80% success rate)
- **Medium** (40-60% success rate)
- **Hard** (20-40% success rate)
- **Expert** (<20% success rate)

**Auto-Calibration:**
- Based on usage statistics
- Adjust difficulty over time
- Track success rates
- Update automatically

## 🔍 Search & Filter

### Search

**Search By:**
- Question text
- Tags
- Category
- Difficulty
- Question type
- Date range

**Advanced Search:**
- Multiple criteria
- Boolean operators (AND, OR)
- Wildcards
- Regex support

### Filters

**Quick Filters:**
- Unused questions
- High success rate
- Low success rate
- Recently added
- Most used
- Needs review

**Custom Filters:**
- Save filter sets
- Apply quickly
- Share with team
- Export filtered results

## 📝 Using Question Bank

### Create Exam from Bank

**Quick Create:**
```
Exams → Create → Import from Bank

Filters:
- Category: Laravel
- Difficulty: Medium
- Count: 20 questions

Auto-generate exam!
```

**Smart Selection:**
- Balance difficulty
- Mix question types
- Avoid recently used
- Optimize for time
- Random selection

### Question Pools

**Create Pools:**
```
Topic: Laravel Basics
├── 50 questions total
├── Random pick: 10 per exam
├── Different for each student
└── Same difficulty level
```

**Benefits:**
- Prevent cheating
- Fair assessment
- Reusable
- Scalable

## 📊 Question Statistics

### Usage Analytics

**Metrics:**
- Times used
- Exams included
- Student attempts
- Average score
- Time spent
- Skip rate

**Performance:**
```
Question ID: Q-001
├── Used: 25 times
├── Success Rate: 75%
├── Avg. Time: 1.5 min
├── Difficulty: Medium (actual)
└── Discrimination Index: 0.42
```

### Quality Metrics

**Item Analysis:**
- **Difficulty Index** = (Correct / Total) × 100
- **Discrimination Index** = Top 27% - Bottom 27%
- **Distractor Analysis** = Wrong answer distribution

**Quality Indicators:**
- ✅ Good (DI > 0.3)
- ⚠️ Review (DI: 0.15-0.3)
- ❌ Poor (DI < 0.15)

## 🔧 Question Maintenance

### Editing Questions

**Version Control:**
- Track changes
- View history
- Revert changes
- Compare versions
- Audit trail

**Bulk Edit:**
- Select multiple
- Change category
- Update difficulty
- Add tags
- Set points

### Quality Review

**Review Process:**
1. Flag for review
2. Expert evaluation
3. Update/improve
4. Re-test
5. Approve

**Review Criteria:**
- Clarity
- Accuracy
- Difficulty
- Fairness
- Relevance

### Archiving

**Archive Old Questions:**
- No longer relevant
- Replaced by better ones
- Outdated content
- Quality issues

**Benefits:**
- Clean database
- Better search
- Current content
- Can restore if needed

## 📤 Export Options

### Export Formats

**Excel:**
- Formatted
- Multiple sheets
- Filter included
- Images embedded

**CSV:**
- Plain text
- Import-ready
- Lightweight
- Universal

**PDF:**
- Print-ready
- Question bank book
- Study materials
- Archive copy

**QTI (Question and Test Interoperability):**
- Standard format
- Cross-platform
- LMS compatible
- Industry standard

## 🎯 Best Practices

### Creating Quality Questions

**Good Questions:**
```
✓ Clear and unambiguous
✓ One correct answer (MC)
✓ Plausible distractors
✓ Appropriate difficulty
✓ Test understanding, not memory
```

**Avoid:**
```
✗ Trick questions
✗ Ambiguous wording
✗ Obvious answers
✗ Too easy/hard
✗ Outdated content
```

### Organization Tips

**Consistent Naming:**
- Use naming conventions
- Add prefixes/codes
- Include topics
- Version numbers

**Tagging Strategy:**
- Use standard tags
- Not too many
- Be specific
- Maintain taxonomy

## 🔐 Access Control

### Permissions

**Who Can:**
- **View**: All instructors
- **Create**: Instructors + Admins
- **Edit Own**: Question author
- **Edit All**: Admins only
- **Delete**: Admins only
- **Export**: Instructors + Admins

**Sharing:**
- Public (all instructors)
- Private (author only)
- Team (selected instructors)
- School-specific

## 📱 Mobile Access

**Mobile Features:**
- Browse questions
- Quick search
- Add simple questions
- Review flagged
- Export
- Statistics view

## 🐛 Troubleshooting

### Common Issues

**Import Failed:**
- Check file format
- Verify column names
- Review data validation
- Check file encoding

**Question Not Showing:**
- Check filters
- Verify status (active/archived)
- Check permissions
- Clear cache

**Statistics Wrong:**
- Refresh cache
- Check date range
- Verify exam submissions
- Recalculate stats

## 📊 Reports

### Question Bank Reports

**Inventory Report:**
- Total questions
- By category
- By difficulty
- By type
- By usage

**Quality Report:**
- High performers
- Need review
- Low quality
- Unused
- Most popular

**Usage Report:**
- Most used
- Least used
- Success rates
- Time analysis
- Student feedback

## 📞 Support

**Need Help?**
- Question creation tips
- Import templates
- Best practices
- Technical support

**Resources:**
- Video tutorials
- Example questions
- Quality guidelines
- Writing workshops

---

**Question Bank - Complete! 💾✨**

Comprehensive question management system dengan organization, analytics, dan quality control!

