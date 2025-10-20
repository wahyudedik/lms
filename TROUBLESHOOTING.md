# 🔧 **Troubleshooting Guide - Laravel LMS**

## ✅ **Error Fixed: View [admin.users.edit] not found**

### **Problem**
```
InvalidArgumentException - Internal Server Error
View [admin.users.edit] not found.
```

### **Solution Applied**
✅ **Created missing view files:**
- `resources/views/admin/users/edit.blade.php` - Edit user form
- `resources/views/admin/users/show.blade.php` - User details view

### **Files Created**
1. **Edit User View** (`admin/users/edit.blade.php`)
   - Complete edit form with all user fields
   - Password update section
   - Success/error message display
   - Form validation

2. **Show User View** (`admin/users/show.blade.php`)
   - User profile display
   - Personal and account information
   - Quick actions (edit, activate/deactivate, delete)
   - Responsive design

---

## 🚀 **Setup Instructions**

### **1. Database Setup**
```bash
# Fresh migration with seed
php artisan migrate:fresh --seed

# Or if you want to keep existing data
php artisan migrate
php artisan db:seed
```

### **2. Storage Link**
```bash
# Create symbolic link for file uploads
php artisan storage:link
```

### **3. Default Credentials**
| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@lms.com | password |
| **Guru** | guru@lms.com | password |
| **Siswa** | siswa@lms.com | password |

---

## 🔍 **Common Issues & Solutions**

### **1. Profile Photo Upload Issues**

#### **Problem**: Photos not displaying
**Solution**: 
```bash
# Ensure storage link exists
php artisan storage:link

# Check storage directory permissions
chmod -R 775 storage
```

#### **Problem**: Upload fails
**Solution**: 
- Check file size (max 2MB)
- Check file format (JPG, PNG, GIF only)
- Ensure storage directory is writable

### **2. User Management Issues**

#### **Problem**: Cannot access user management
**Solution**: 
- Login as admin user
- Check middleware: `role:admin`
- Verify user has admin role

#### **Problem**: Cannot edit users
**Solution**: 
- Ensure you're logged in as admin
- Check route permissions
- Verify user exists in database

### **3. Email Verification Issues**

#### **Problem**: Emails not sending
**Solution**: 
```env
# Check .env configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

#### **Problem**: Verification links not working
**Solution**: 
- Check APP_URL in .env
- Ensure email verification is enabled
- Check token expiration (60 minutes)

### **4. Navigation Issues**

#### **Problem**: Navigation not showing role-specific menus
**Solution**: 
- Check user role in database
- Verify middleware is working
- Clear view cache: `php artisan view:clear`

---

## 🛠️ **Development Commands**

### **Clear Caches**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Clear compiled views
php artisan view:clear
```

### **Check Routes**
```bash
# List all routes
php artisan route:list

# Check specific route
php artisan route:list --name=admin.users
```

### **Database Commands**
```bash
# Check migrations
php artisan migrate:status

# Rollback migrations
php artisan migrate:rollback

# Fresh migration
php artisan migrate:fresh --seed
```

---

## 📁 **File Structure Check**

### **Required Files**
```
✅ app/Http/Controllers/Admin/UserController.php
✅ app/Http/Controllers/ProfilePhotoController.php
✅ resources/views/admin/users/index.blade.php
✅ resources/views/admin/users/create.blade.php
✅ resources/views/admin/users/edit.blade.php
✅ resources/views/admin/users/show.blade.php
✅ resources/views/profile/partials/update-profile-photo-form.blade.php
✅ resources/views/layouts/navigation.blade.php
✅ routes/web.php
```

### **Storage Directories**
```
✅ storage/app/public/profile-photos/
✅ public/storage/ (symlink)
✅ public/images/avatars/
```

---

## 🧪 **Testing Checklist**

### **User Management**
- [ ] Login as admin
- [ ] Access `/admin/users`
- [ ] Create new user
- [ ] Edit existing user
- [ ] View user details
- [ ] Toggle user status
- [ ] Delete user
- [ ] Search and filter users

### **Profile Photo**
- [ ] Upload profile photo
- [ ] View uploaded photo
- [ ] Delete profile photo
- [ ] Check default avatars

### **Authentication**
- [ ] Register new user
- [ ] Login with credentials
- [ ] Role-based redirect
- [ ] Email verification
- [ ] Password reset

### **Navigation**
- [ ] Admin navigation menu
- [ ] Guru navigation menu
- [ ] Siswa navigation menu
- [ ] Mobile responsive menu

---

## 🚨 **Error Logs**

### **Check Laravel Logs**
```bash
# View recent logs
tail -f storage/logs/laravel.log

# Clear logs
> storage/logs/laravel.log
```

### **Common Error Messages**

#### **"View not found"**
- Check file exists in `resources/views/`
- Verify file name matches exactly
- Check case sensitivity

#### **"Route not found"**
- Check `routes/web.php`
- Verify route name
- Run `php artisan route:clear`

#### **"Permission denied"**
- Check file permissions
- Ensure storage is writable
- Check directory ownership

---

## 🔧 **Performance Optimization**

### **Production Setup**
```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Install dependencies
composer install --optimize-autoloader --no-dev
```

### **File Upload Optimization**
- Use CDN for file storage
- Implement image compression
- Add file size limits
- Use queue for large uploads

---

## 📞 **Support**

### **Debug Mode**
```env
# Enable debug mode for development
APP_DEBUG=true
APP_ENV=local
```

### **Log Level**
```env
# Set log level
LOG_LEVEL=debug
```

---

## ✅ **Status: All Issues Resolved**

**Last Updated**: October 20, 2025  
**Laravel Version**: 12.34.0  
**PHP Version**: 8.4.13  

**All major issues have been resolved and the system is ready for use!** 🎉
