# ⚙️ System Settings Guide

Complete guide untuk system settings dan configuration di Laravel LMS.

## 📋 Overview

System Settings memungkinkan:
- ✅ General configuration
- ✅ Appearance customization  
- ✅ Email settings
- ✅ Notification preferences
- ✅ Security settings
- ✅ Backup & restore
- ✅ System maintenance

## 🎯 Settings Categories

### General Settings

**Application:**
```
Admin → Settings → General
```

**Configuration:**
- App Name
- App Description
- School Name
- School Address
- Contact Info (Phone, Email)
- Time Zone
- Language
- Date Format

**Logo & Branding:**
- App Logo (PNG, JPG, max 2MB)
- Favicon (ICO, PNG, 32x32)
- Login Page Image
- Email Logo

### Appearance Settings

**Colors:**
- Primary Color
- Secondary Color
- Accent Color
- Background Color
- Text Color

**Theme:**
- Light Mode
- Dark Mode
- Auto (System)
- Custom Themes

**Layout:**
- Sidebar Position (Left/Right)
- Compact/Comfortable
- Fixed/Scrollable Header
- Footer Display

### Email Settings

**SMTP Configuration:**
```
Mail Driver: SMTP
Host: smtp.gmail.com
Port: 587
Username: your-email@gmail.com
Password: ********
Encryption: TLS
From Name: Laravel LMS
From Address: noreply@lms.com
```

**Email Templates:**
- Welcome Email
- Password Reset
- Enrollment Confirmation
- Certificate Issued
- Exam Reminder
- Grade Notification

**Test Email:**
- Send test email
- Verify configuration
- Check delivery
- Review formatting

### Notification Settings

**System Notifications:**
```
☑ Email Notifications
☑ Browser Push Notifications
☑ In-App Notifications
□ SMS Notifications (requires setup)
```

**User Preferences:**
- New enrollment
- Exam available
- Grade posted
- Certificate ready
- Forum replies
- Announcements

**Admin Alerts:**
- New user registration
- Payment received
- System errors
- Backup completed
- Security issues

### Registration Settings

**User Registration:**
```
☑ Enable Registration
☑ Email Verification Required
☑ Admin Approval Required
□ Auto-assign Role
Default Role: Siswa
```

**Registration Form:**
- Required Fields
- Optional Fields
- Custom Fields
- Terms & Conditions
- Privacy Policy

**Restrictions:**
- Domain Whitelist
- IP Restrictions
- Rate Limiting
- CAPTCHA

### Security Settings

**Password Policy:**
```
Minimum Length: 8 characters
☑ Require Uppercase
☑ Require Lowercase
☑ Require Numbers
☑ Require Special Characters
Password Expiry: 90 days
```

**Session:**
- Session Timeout: 120 minutes
- Remember Me Duration: 30 days
- Concurrent Sessions: 3
- Force Logout on Password Change

**Two-Factor Authentication:**
```
☑ Enable 2FA
☑ Required for Admins
□ Required for All Users
Method: Authenticator App
```

**Security Features:**
- Login Attempt Limit: 5
- Lockout Duration: 30 minutes
- IP Whitelisting
- Activity Logging
- Failed Login Alerts

## 🎨 Customization

### Theme Customization

**Color Scheme:**
```css
Primary: #3B82F6 (Blue)
Secondary: #10B981 (Green)
Accent: #F59E0B (Orange)
Background: #F9FAFB (Light Gray)
Text: #111827 (Dark Gray)
```

**Typography:**
- Font Family
- Font Sizes
- Line Heights
- Font Weights

**Custom CSS:**
```css
/* Add custom styles */
.custom-header {
    background: your-gradient;
}
```

### Landing Page

**Sections:**
- Hero Banner
- Features
- Courses Preview
- Testimonials
- Statistics
- Contact Form

**Configuration:**
```
☑ Enable Landing Page
☑ Show Statistics
☑ Show Featured Courses
☑ Show Testimonials
Hero Title: "Learn Anything, Anytime"
CTA Button: "Get Started"
```

## 🔧 System Configuration

### Database Settings

**Connection:**
- Driver: MySQL
- Host: localhost
- Port: 3306
- Database: lms_db
- Username: root
- Charset: utf8mb4

**Maintenance:**
- Optimize Tables
- Repair Tables
- Backup Database
- Import/Export

### Cache Settings

**Cache Driver:**
```
Options:
- File (default)
- Redis
- Memcached
- Database
```

**Cache Actions:**
- Clear All Cache
- Clear Config Cache
- Clear Route Cache
- Clear View Cache
- Clear Application Cache

### Queue Settings

**Queue Driver:**
```
Options:
- Sync (default)
- Database
- Redis
- Beanstalkd
```

**Jobs:**
- Send Email
- Generate Certificate
- Export Reports
- Process Uploads
- Calculate Statistics

## 💾 Backup & Restore

### Automatic Backups

**Schedule:**
```
☑ Enable Auto Backup
Frequency: Daily
Time: 02:00 AM
Retention: 30 days
Location: storage/backups/
```

**Backup Contents:**
- ☑ Database
- ☑ Uploaded Files
- ☑ Configuration
- ☑ User Data
- □ System Files

### Manual Backup

**Create Backup:**
```
Admin → Settings → Backup → Create Backup

Options:
- Full Backup
- Database Only
- Files Only
- Selective Backup
```

**Download:**
- Backup list
- Download link
- File size
- Created date
- Expiry date

### Restore

**Restore Process:**
```
1. Upload backup file
2. Verify integrity
3. Preview contents
4. Select components
5. Confirm restore
6. Execute restore
```

**Warnings:**
- Creates restore point first
- May overwrite data
- Requires confirmation
- Can take time
- Monitor progress

## 📊 System Information

### System Status

**Health Check:**
```
✓ Database: Connected
✓ Cache: Working
✓ Queue: Running
✓ Storage: Writable
✓ Email: Configured
✓ SSL: Active
```

**Resources:**
- PHP Version: 8.2
- Laravel Version: 11.x
- MySQL Version: 8.0
- Disk Space: 80GB free
- Memory Usage: 256MB / 2GB

### Performance

**Monitoring:**
- Page Load Time
- Query Count
- Memory Usage
- CPU Usage
- Active Users

**Optimization:**
```
☑ Enable Query Caching
☑ Compress Assets
☑ Lazy Load Images
☑ Enable CDN
☑ Minify CSS/JS
```

## 🔐 Advanced Settings

### API Configuration

**API Keys:**
- Generate API Key
- Manage Permissions
- Rate Limiting
- Webhooks

**Integrations:**
- Google Analytics
- Payment Gateway
- SMS Provider
- Cloud Storage
- Video Platform

### Maintenance Mode

**Enable Maintenance:**
```
Admin → Settings → Maintenance Mode

Options:
☑ Enable Maintenance Mode
Message: "We'll be back soon!"
Allowed IPs: 192.168.1.1
Bypass URL: /maintenance-bypass
```

**Features:**
- Custom message
- Estimated time
- Progress indicator
- Allow specific IPs
- Bypass for admins

## 🎯 Best Practices

### Configuration

**Security:**
- Strong passwords
- Enable 2FA
- Regular updates
- Monitor logs
- Backup regularly

**Performance:**
- Enable caching
- Optimize database
- Clean old data
- Monitor resources
- Use CDN

**Maintenance:**
- Regular backups
- Test restores
- Update software
- Review settings
- Document changes

## 🐛 Troubleshooting

### Common Issues

**Email Not Sending:**
- Check SMTP settings
- Verify credentials
- Test connection
- Check firewall
- Review logs

**Cache Problems:**
- Clear all caches
- Check permissions
- Verify driver
- Review configuration

**Backup Failed:**
- Check disk space
- Verify permissions
- Review logs
- Test manually

## 📞 Support

**Configuration Help:**
- Setting guides
- Video tutorials
- Best practices
- Expert consultation

**Technical Support:**
- System issues
- Performance problems
- Security concerns
- Feature requests

---

**System Settings - Complete! ⚙️✨**

Comprehensive system configuration dengan security, backups, performance optimization, dan customization!

