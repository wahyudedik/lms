# 📚 Course Management Guide

Complete guide untuk mengelola courses di Laravel LMS.

## 📋 Overview

Course Management System memungkinkan:
- ✅ Create & organize courses
- ✅ Manage materials & exams
- ✅ Track student progress
- ✅ Assign instructors
- ✅ Control enrollment
- ✅ Generate certificates

## 🎯 Course Features

### Course Structure

```
Course
├── Basic Information
│   ├── Title & Description
│   ├── Category & Level
│   ├── Duration
│   └── Thumbnail
├── Materials (Lessons)
│   ├── Video lessons
│   ├── Documents
│   ├── Presentations
│   └── Links
├── Exams (Assessments)
│   ├── Quizzes
│   ├── Mid-term
│   ├── Final exam
│   └── Assignments
├── Enrollments
│   ├── Active students
│   ├── Progress tracking
│   ├── Completion status
│   └── Certificates
└── Settings
    ├── Visibility
    ├── Prerequisites
    ├── Price (if paid)
    └── Certificate template
```

## 🚀 Creating a Course

### Step 1: Basic Information

```
Admin/Guru → Courses → Create New
```

**Required Fields:**
- **Title**: Clear & descriptive
- **Description**: Course overview
- **Instructor**: Assign teacher
- **Category**: Subject area
- **Level**: Beginner/Intermediate/Advanced

**Optional:**
- **Duration**: Estimated hours
- **Prerequisites**: Required courses
- **Max Students**: Enrollment limit
- **Price**: For paid courses

### Step 2: Upload Thumbnail

**Specifications:**
- Format: JPG, PNG
- Size: Max 2MB
- Dimensions: 1280x720 (16:9)
- Quality: High resolution

**Tips:**
- Use relevant images
- Professional look
- Include text overlay
- Brand consistency

### Step 3: Add Materials

```
Courses → [Your Course] → Materials → Add Material
```

**Material Types:**

**1. Video:**
- Upload video file
- YouTube embed
- Vimeo link
- Duration tracking

**2. Document:**
- PDF upload
- Word documents
- Presentations
- Download option

**3. Text Content:**
- Rich text editor
- Formatted content
- Images & media
- Code blocks

**4. External Link:**
- Articles
- Resources
- Tools
- References

### Step 4: Create Exams

```
Courses → [Your Course] → Exams → Create Exam
```

**Exam Types:**
- **Quiz**: Short assessment (5-10 questions)
- **Test**: Medium assessment (10-20 questions)
- **Midterm**: Major assessment (20-40 questions)
- **Final**: Comprehensive exam (40+ questions)

### Step 5: Publish Course

```
Edit Course → ☑ Active → Save
```

Course now visible to students!

## 📊 Managing Materials

### Organization

**Ordering:**
- Drag & drop
- Sequential learning
- Logical flow
- Module grouping

**Visibility:**
- All visible
- Sequential unlock
- Date-based release
- Prerequisite-based

### Material Types

**Video Lessons:**
```
Features:
- Video player
- Progress tracking
- Speed control
- Fullscreen mode
- Mobile support
```

**Documents:**
```
Features:
- PDF viewer
- Download option
- Print option
- Search within doc
- Bookmark support
```

**Interactive:**
```
Features:
- Quizzes
- Exercises
- Simulations
- Code playground
```

## 👥 Student Enrollment

### Enrollment Methods

**1. Manual Enrollment:**
```
Course → Enrollments → Enroll Student
Select students → Enroll
```

**2. Self-Enrollment:**
```
Student browses courses → Enroll button
Auto-enrolled (if allowed)
```

**3. Bulk Enrollment:**
```
Import CSV:
student_email, course_id, enrolled_at
user@example.com, 1, 2025-01-01
```

**4. Group Enrollment:**
```
Select course → Enroll all students from class
```

### Managing Enrollments

**Track Progress:**
- Materials completed
- Exams taken
- Time spent
- Last activity

**Enrollment Status:**
- **Active**: Currently learning
- **Completed**: Finished course
- **Dropped**: Withdrawn

**Actions:**
- View progress
- Send message
- Reset progress
- Issue certificate
- Remove enrollment

## 📈 Progress Tracking

### Student Progress

**Automatic Calculation:**
```php
progress = (completed_materials / total_materials) × 100%
```

**Completion Criteria:**
```
1. All materials viewed
2. All exams taken
3. Pass score achieved
4. Minimum time spent
```

**Progress Indicators:**
- Progress bar
- Percentage
- Materials count
- Time spent
- Badges/achievements

### Course Analytics

**Metrics:**
- Total enrollments
- Active learners
- Completion rate
- Average score
- Time to complete
- Drop rate

**Reports:**
- Student list
- Progress report
- Grade book
- Attendance
- Activity log

## 🎓 Course Completion

### Completion Requirements

**Default:**
```
☑ All materials completed (100%)
☑ All exams taken
☑ Average score ≥ 60%
```

**Custom:**
```
Configure per course:
- Required materials only
- Specific exams
- Minimum score
- Minimum time
- Attendance requirement
```

### Certificate Generation

**Automatic:**
```
When student completes:
1. Check requirements
2. Calculate final score
3. Generate certificate
4. Send notification
5. Record in database
```

**Manual:**
```
Admin → Student → Issue Certificate
```

## 🎨 Course Customization

### Course Settings

**Visibility:**
- Public (all can see)
- Private (enrolled only)
- Hidden (admin only)
- Scheduled (date-based)

**Access Control:**
- Open enrollment
- Approval required
- Invitation only
- Payment required

**Features:**
- Discussion forum
- Live sessions
- Assignments
- Peer review
- Certificates

### Branding

**Course Page:**
- Custom banner
- Color scheme
- Logo
- Footer info

**Certificates:**
- Template selection
- Custom text
- Signatures
- Logo placement

## 📱 Student View

### Course Page

**Sections:**
```
┌─────────────────────────────┐
│  Course Banner              │
│  Title & Instructor         │
├─────────────────────────────┤
│  [Enroll Button]            │
├─────────────────────────────┤
│  Overview                   │
│  - Description              │
│  - Duration                 │
│  - Level                    │
│  - Prerequisites            │
├─────────────────────────────┤
│  What You'll Learn          │
│  ☑ Topic 1                  │
│  ☑ Topic 2                  │
├─────────────────────────────┤
│  Course Content             │
│  📚 Materials (10)          │
│  📝 Exams (3)               │
├─────────────────────────────┤
│  Instructor                 │
│  👤 [Profile]               │
└─────────────────────────────┘
```

### Learning Interface

**Navigation:**
- Previous/Next buttons
- Sidebar with materials
- Progress indicator
- Breadcrumbs

**Features:**
- Mark as complete
- Add notes
- Download resources
- Report issues
- Rate content

## 🎯 Best Practices

### Course Design

**1. Clear Structure:**
```
Module 1: Introduction (Week 1)
├── Lesson 1.1: Overview
├── Lesson 1.2: Basics
└── Quiz 1

Module 2: Core Concepts (Week 2)
├── Lesson 2.1: Theory
├── Lesson 2.2: Practice
└── Test 2

Module 3: Advanced Topics (Week 3)
├── Lesson 3.1: Advanced
├── Lesson 3.2: Case Studies
└── Final Exam
```

**2. Engaging Content:**
- Mix media types
- Interactive elements
- Real examples
- Practice exercises
- Assessments

**3. Reasonable Pace:**
```
- 1-2 hours per week minimum
- Break into small chunks
- Regular assessments
- Review sessions
- Flexible deadlines
```

### Instructor Tips

**Content Creation:**
- Plan ahead
- Use templates
- Reuse materials
- Update regularly
- Get feedback

**Student Engagement:**
- Regular announcements
- Quick responses
- Personalized feedback
- Recognition badges
- Community building

## 🔧 Advanced Features

### Prerequisites

**Chain Courses:**
```
Course A → Course B → Course C
(Must complete in order)
```

**Requirements:**
- Completed courses
- Minimum scores
- Skills/badges
- Time restrictions

### Cohort Management

**Group Learning:**
- Start/end dates
- Synchronized progress
- Group activities
- Peer interaction
- Scheduled sessions

### Adaptive Learning

**Personalization:**
- Skill assessment
- Custom path
- Difficulty adjustment
- Recommended content
- Smart scheduling

## 📊 Reports & Analytics

### Course Reports

**Performance:**
- Student scores
- Completion rates
- Time analytics
- Engagement metrics
- Feedback scores

**Content Analytics:**
- Popular materials
- Difficult topics
- Drop-off points
- Time per material
- Resource downloads

### Export Options

**Formats:**
- Excel spreadsheet
- CSV file
- PDF report
- JSON data

**Data:**
- Enrollments
- Grades
- Progress
- Certificates
- Activity logs

## 🐛 Troubleshooting

### Common Issues

**Materials Not Loading:**
- Check file permissions
- Verify file size
- Test different browser
- Clear cache

**Progress Not Updating:**
- Refresh page
- Check completion criteria
- Verify database
- Review logs

**Enrollment Problems:**
- Check course status
- Verify prerequisites
- Review access settings
- Check user role

## 📞 Support

**For Instructors:**
- Course creation help
- Content best practices
- Technical support
- Training resources

**For Students:**
- Enrollment issues
- Access problems
- Content questions
- Certificate requests

---

**Course Management - Complete! 📚✨**

Comprehensive system untuk create, organize, dan manage courses dengan materials, exams, dan progress tracking!

