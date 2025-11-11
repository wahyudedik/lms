# 👥 User Management Guide

Complete guide untuk managing users di Laravel LMS.

## 📋 Overview

User Management memungkinkan:
- ✅ Create & manage users
- ✅ Role-based access control
- ✅ Bulk operations
- ✅ Import/Export users
- ✅ Activity tracking
- ✅ Profile management

## 🎯 User Roles

### Role Hierarchy

```
Admin (Superadmin)
├── Full system access
├── Manage all users
├── System settings
└── All features

Guru (Instructor/Teacher)
├── Manage own courses
├── Create exams
├── Grade students
└── View analytics

Siswa (Student)
├── Enroll in courses
├── Take exams
├── View progress
└── Participate in forums
```

### Role Permissions

**Admin:**
- ✅ User management
- ✅ Course management
- ✅ System settings
- ✅ Backup & restore
- ✅ View all data
- ✅ Generate reports
- ✅ Manage schools
- ✅ Forum moderation

**Guru:**
- ✅ Create courses
- ✅ Manage materials
- ✅ Create exams
- ✅ Grade students
- ✅ View course analytics
- ✅ Manage enrollments
- ❌ System settings
- ❌ User management

**Siswa:**
- ✅ Browse courses
- ✅ Enroll in courses
- ✅ View materials
- ✅ Take exams
- ✅ View own progress
- ✅ Forum participation
- ❌ Create courses
- ❌ Grade others

## 🚀 Managing Users

### Creating Users

```
Admin → Users → Create New User
```

**Required Fields:**
- Name
- Email (unique)
- Password
- Role

**Optional:**
- Phone number
- Address
- Date of birth
- Profile photo
- Bio

### Bulk User Creation

**Via CSV Import:**
```csv
name,email,password,role
John Doe,john@example.com,password123,siswa
Jane Smith,jane@example.com,password123,guru
```

**Steps:**
1. Download template
2. Fill in user data
3. Upload CSV
4. Review preview
5. Import users

**Validation:**
- Unique emails
- Valid roles
- Required fields
- Format checking

### User Profile

**Information:**
- Personal details
- Contact info
- Profile photo
- Bio/description
- Social links

**Statistics:**
- Enrolled courses
- Completed courses
- Exam scores
- Forum activity
- Certificates earned

## 📊 User Management

### User List

**View Options:**
- Table view
- Card view
- Export to Excel
- Print list

**Filters:**
- By role
- By status
- By school
- By registration date
- By activity

**Search:**
- By name
- By email
- By ID
- Advanced search

### User Actions

**Single User:**
- View profile
- Edit details
- Change password
- Change role
- Suspend/Activate
- Delete user
- Send email

**Bulk Actions:**
- Select multiple
- Change role
- Suspend/Activate
- Delete users
- Export selected
- Send bulk email

## 🔐 Access Control

### Account Status

**Active:**
- Can login
- Full access
- Normal usage

**Suspended:**
- Cannot login
- Access blocked
- Temporary state
- Can be reactivated

**Inactive:**
- Not verified
- Pending approval
- Limited access
- Email verification needed

### Password Management

**Admin Actions:**
- Reset password
- Force password change
- Send reset link
- Set temporary password

**User Actions:**
- Change password
- Forgot password
- Password requirements:
  - Min 8 characters
  - Mixed case
  - Numbers
  - Special characters

### Two-Factor Authentication

**Setup:**
- Enable 2FA
- Scan QR code
- Verify code
- Save recovery codes

**Benefits:**
- Extra security
- Prevent unauthorized access
- Required for admin (optional)

## 👤 Profile Management

### User Profile

**Editable Fields:**
- Name
- Email
- Phone
- Address
- Date of birth
- Profile photo
- Bio

**Display Settings:**
- Privacy options
- Notification preferences
- Email settings
- Theme selection

### Profile Photo

**Upload:**
- JPG, PNG formats
- Max 2MB size
- Crop & resize
- Auto-thumbnail

**Display:**
- User menu
- Forum posts
- Comments
- Course list
- Certificates

## 📈 User Analytics

### Activity Tracking

**Metrics:**
- Last login
- Total login count
- Time spent
- Pages visited
- Actions performed

**Course Activity:**
- Enrolled courses
- Completed materials
- Exam attempts
- Progress percentage
- Certificates earned

**Forum Activity:**
- Threads created
- Replies posted
- Likes received
- Solutions provided
- Reputation score

### Reports

**User Report:**
- Personal info
- Course progress
- Exam scores
- Activity log
- Achievements

**Export Options:**
- PDF report
- Excel spreadsheet
- JSON data
- Print-friendly

## 🏫 School Assignment

### Assign to School

**For Multi-School Setup:**
```
Edit User → School → Select School → Save
```

**Features:**
- School branding
- School-specific courses
- School reports
- School admins

**Benefits:**
- Organized management
- Separate analytics
- Custom settings
- Brand consistency

## 🔧 Advanced Features

### User Groups

**Create Groups:**
- Class/Cohort
- Department
- Custom groups

**Group Actions:**
- Bulk enroll
- Group messaging
- Shared resources
- Group analytics

### Custom Fields

**Add Extra Fields:**
- Student ID
- Employee ID
- Department
- Grade level
- Custom tags

**Usage:**
- Filtering
- Reporting
- Integration
- Custom workflows

### API Access

**For Integrations:**
- API tokens
- OAuth2
- Webhooks
- SSO integration

## 📱 Mobile Access

**Mobile Features:**
- Responsive design
- Mobile app ready
- Profile editing
- Photo upload
- Notifications

## 🎯 Best Practices

### User Creation

**Conventions:**
- Standard email format
- Strong passwords
- Complete profiles
- Verify emails
- Assign correct roles

### Security

**Recommendations:**
- Regular password changes
- Monitor suspicious activity
- Review permissions
- Audit logs
- Backup user data

### Data Privacy

**GDPR Compliance:**
- User consent
- Data export
- Right to delete
- Privacy policy
- Terms of service

## 🐛 Troubleshooting

### Login Issues

**Common Problems:**
- Forgotten password → Reset link
- Email not verified → Resend verification
- Account suspended → Contact admin
- Wrong credentials → Check caps lock

### Profile Issues

**Solutions:**
- Photo not uploading → Check size/format
- Email already exists → Use different email
- Cannot edit → Check permissions
- Data not saving → Clear cache

## 📊 User Reports

### Generate Reports

**Report Types:**
- All users
- By role
- By school
- Active users
- Inactive users
- New registrations

**Data Included:**
- Personal info
- Role & status
- Registration date
- Last login
- Course progress
- Exam scores

### Export Formats

- Excel (.xlsx)
- CSV
- PDF
- JSON

## 📞 Support

**Admin Support:**
- User management help
- Technical issues
- Feature requests
- Training resources

**User Support:**
- Account issues
- Profile help
- Access problems
- General questions

---

**User Management - Complete! 👥✨**

Comprehensive user management dengan roles, permissions, bulk operations, dan analytics!

