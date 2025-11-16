<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ==================================================
// GUEST EXAM ACCESS (Public - No Authentication)
// ==================================================
Route::prefix('exam')->name('guest.exams.')->group(function () {
    Route::get('/', [App\Http\Controllers\GuestExamController::class, 'index'])->name('index');
    Route::post('/verify-token', [App\Http\Controllers\GuestExamController::class, 'verifyToken'])->name('verify-token');
    Route::get('/{exam}/info', [App\Http\Controllers\GuestExamController::class, 'showInfo'])->name('info');
    Route::post('/{exam}/start', [App\Http\Controllers\GuestExamController::class, 'start'])->name('start');
    Route::get('/attempt/{attempt}/take', [App\Http\Controllers\GuestExamController::class, 'take'])->name('take');
    Route::post('/attempt/{attempt}/answer', [App\Http\Controllers\GuestExamController::class, 'saveAnswer'])->name('save-answer');
    Route::post('/attempt/{attempt}/submit', [App\Http\Controllers\GuestExamController::class, 'submit'])->name('submit');
    Route::get('/attempt/{attempt}/review', [App\Http\Controllers\GuestExamController::class, 'review'])->name('review');
    Route::post('/attempt/{attempt}/violation', [App\Http\Controllers\GuestExamController::class, 'logViolation'])->name('log-violation');
});

// Default dashboard (fallback)
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // ✅ FIX BUG #13: Add safety check for dashboard route
    try {
        $dashboardRoute = $user->dashboard_route;
        if (!$dashboardRoute) {
            throw new \Exception('Dashboard route not configured');
        }
        return redirect()->route($dashboardRoute);
    } catch (\Exception $e) {
        // Fallback to login if dashboard route is not available
        return redirect()->route('login')->with('error', 'Dashboard tidak tersedia. Silakan hubungi administrator.');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Dashboard
Route::get('/admin/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.dashboard');

// Admin User Management & Course Management
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // User Management
    // Import/Export Routes (must be before resource routes)
    Route::get('users/export', [App\Http\Controllers\Admin\UserController::class, 'export'])->name('users.export');
    Route::get('users/import', [App\Http\Controllers\Admin\UserController::class, 'importForm'])->name('users.import');
    Route::post('users/import', [App\Http\Controllers\Admin\UserController::class, 'import'])->name('users.import.store');
    Route::get('users/template', [App\Http\Controllers\Admin\UserController::class, 'downloadTemplate'])->name('users.template');

    // Resource routes
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/reset-login', [App\Http\Controllers\Admin\UserController::class, 'resetLogin'])->name('users.reset-login');
    Route::patch('users/{user}/password', [App\Http\Controllers\Admin\UserController::class, 'updatePassword'])->name('users.update-password');

    // Cheating incidents
    Route::resource('cheating-incidents', App\Http\Controllers\Admin\CheatingIncidentController::class)->only(['index', 'show']);
    Route::post('cheating-incidents/{cheatingIncident}/resolve', [App\Http\Controllers\Admin\CheatingIncidentController::class, 'resolve'])->name('cheating-incidents.resolve');

    // Course Management
    Route::resource('courses', App\Http\Controllers\Admin\CourseController::class);
    Route::post('courses/{course}/toggle-status', [App\Http\Controllers\Admin\CourseController::class, 'toggleStatus'])->name('courses.toggle-status');

    // Enrollment Management
    Route::get('courses/{course}/enrollments', [App\Http\Controllers\EnrollmentController::class, 'index'])->name('courses.enrollments');
    Route::post('courses/{course}/enrollments', [App\Http\Controllers\EnrollmentController::class, 'store'])->name('courses.enrollments.store');
    Route::delete('courses/{course}/enrollments/{enrollment}', [App\Http\Controllers\EnrollmentController::class, 'destroy'])->name('courses.enrollments.destroy');
    Route::patch('courses/{course}/enrollments/{enrollment}/status', [App\Http\Controllers\EnrollmentController::class, 'updateStatus'])->name('courses.enrollments.update-status');
    Route::patch('courses/{course}/enrollments/{enrollment}/progress', [App\Http\Controllers\EnrollmentController::class, 'updateProgress'])->name('courses.enrollments.update-progress');

    // Material Management
    Route::resource('courses.materials', App\Http\Controllers\Admin\MaterialController::class);
    Route::post('courses/{course}/materials/{material}/toggle-status', [App\Http\Controllers\Admin\MaterialController::class, 'toggleStatus'])->name('courses.materials.toggle-status');
    Route::post('courses/{course}/materials/reorder', [App\Http\Controllers\Admin\MaterialController::class, 'reorder'])->name('courses.materials.reorder');

    // Exam Management
    Route::resource('exams', App\Http\Controllers\Admin\ExamController::class);
    Route::post('exams/{exam}/toggle-status', [App\Http\Controllers\Admin\ExamController::class, 'toggleStatus'])->name('exams.toggle-status');
    Route::post('exams/{exam}/duplicate', [App\Http\Controllers\Admin\ExamController::class, 'duplicate'])->name('exams.duplicate');
    Route::get('exams/{exam}/results', [App\Http\Controllers\Admin\ExamController::class, 'results'])->name('exams.results');

    // Question Management
    Route::resource('exams.questions', App\Http\Controllers\Admin\QuestionController::class);
    Route::post('exams/{exam}/questions/reorder', [App\Http\Controllers\Admin\QuestionController::class, 'reorder'])->name('exams.questions.reorder');
    Route::post('exams/{exam}/questions/{question}/duplicate', [App\Http\Controllers\Admin\QuestionController::class, 'duplicate'])->name('exams.questions.duplicate');

    // Authorization Logs
    Route::get('authorization-logs', [App\Http\Controllers\Admin\AuthorizationLogController::class, 'index'])->name('authorization-logs.index');
    Route::get('authorization-logs/{authorizationLog}', [App\Http\Controllers\Admin\AuthorizationLogController::class, 'show'])->name('authorization-logs.show');
    Route::get('authorization-logs/export/csv', [App\Http\Controllers\Admin\AuthorizationLogController::class, 'export'])->name('authorization-logs.export');

    // Settings & Backup (admin only)
    Route::get('settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');

    // Certificate Settings
    Route::get('certificate-settings', [App\Http\Controllers\Admin\CertificateSettingsController::class, 'index'])->name('certificate-settings.index');
    Route::post('certificate-settings', [App\Http\Controllers\Admin\CertificateSettingsController::class, 'update'])->name('certificate-settings.update');
    Route::post('certificate-settings/reset', [App\Http\Controllers\Admin\CertificateSettingsController::class, 'reset'])->name('certificate-settings.reset');
    Route::get('certificate-settings/preview', [App\Http\Controllers\Admin\CertificateSettingsController::class, 'preview'])->name('certificate-settings.preview');

    // Documentation
    Route::get('documentation', [App\Http\Controllers\Admin\DocumentationController::class, 'index'])->name('documentation.index');
    Route::get('documentation/{slug}', [App\Http\Controllers\Admin\DocumentationController::class, 'show'])->name('documentation.show');

    // AI Settings
    Route::get('ai-settings', [App\Http\Controllers\Admin\AiSettingsController::class, 'index'])->name('ai-settings.index');
    Route::post('ai-settings', [App\Http\Controllers\Admin\AiSettingsController::class, 'update'])->name('ai-settings.update');
    Route::post('ai-settings/reset', [App\Http\Controllers\Admin\AiSettingsController::class, 'reset'])->name('ai-settings.reset');
    Route::post('ai-settings/test', [App\Http\Controllers\Admin\AiSettingsController::class, 'testConnection'])->name('ai-settings.test');
    Route::get('ai-settings/statistics', [App\Http\Controllers\Admin\AiSettingsController::class, 'statistics'])->name('ai-settings.statistics');

    // Database Backup
    Route::get('settings/backup', [App\Http\Controllers\Admin\SettingsController::class, 'backup'])->name('settings.backup');
    Route::post('settings/backup/create', [App\Http\Controllers\Admin\SettingsController::class, 'createBackup'])->name('settings.backup.create');
    Route::get('settings/backup/{filename}/download', [App\Http\Controllers\Admin\SettingsController::class, 'downloadBackup'])->name('settings.backup.download');
    Route::delete('settings/backup/{filename}', [App\Http\Controllers\Admin\SettingsController::class, 'deleteBackup'])->name('settings.backup.delete');

    // Analytics
    Route::get('analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/user-registration-trend', [App\Http\Controllers\Admin\AnalyticsController::class, 'userRegistrationTrend'])->name('analytics.user-registration-trend');
    Route::get('analytics/course-enrollment-stats', [App\Http\Controllers\Admin\AnalyticsController::class, 'courseEnrollmentStats'])->name('analytics.course-enrollment-stats');
    Route::get('analytics/exam-performance-stats', [App\Http\Controllers\Admin\AnalyticsController::class, 'examPerformanceStats'])->name('analytics.exam-performance-stats');
    Route::get('analytics/user-role-distribution', [App\Http\Controllers\Admin\AnalyticsController::class, 'userRoleDistribution'])->name('analytics.user-role-distribution');
    Route::get('analytics/monthly-activity-stats', [App\Http\Controllers\Admin\AnalyticsController::class, 'monthlyActivityStats'])->name('analytics.monthly-activity-stats');

    // Question Bank Management
    Route::prefix('question-bank')->name('question-bank.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\QuestionBankController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\QuestionBankController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\QuestionBankController::class, 'store'])->name('store');

        // Additional actions
        Route::post('/{questionBank}/toggle-verification', [App\Http\Controllers\Admin\QuestionBankController::class, 'toggleVerification'])->name('toggle-verification');
        Route::post('/{questionBank}/duplicate', [App\Http\Controllers\Admin\QuestionBankController::class, 'duplicate'])->name('duplicate');

        // Statistics & Random
        Route::get('/statistics/dashboard', [App\Http\Controllers\Admin\QuestionBankController::class, 'statistics'])->name('statistics');
        Route::post('/get-random', [App\Http\Controllers\Admin\QuestionBankController::class, 'getRandom'])->name('get-random');

        // Import & Export
        Route::get('/download-template', [App\Http\Controllers\Admin\QuestionBankController::class, 'downloadTemplate'])->name('download-template');
        Route::get('/export', [App\Http\Controllers\Admin\QuestionBankController::class, 'export'])->name('export');
        Route::post('/import', [App\Http\Controllers\Admin\QuestionBankController::class, 'import'])->name('import');
        Route::post('/validate-import', [App\Http\Controllers\Admin\QuestionBankController::class, 'validateImport'])->name('validate-import');

        // Import History
        Route::get('/import-history', [App\Http\Controllers\Admin\QuestionBankController::class, 'importHistory'])->name('import-history');
        Route::get('/import-history/{importHistory}', [App\Http\Controllers\Admin\QuestionBankController::class, 'importHistoryShow'])->name('import-history.show');
        Route::delete('/import-history/{importHistory}', [App\Http\Controllers\Admin\QuestionBankController::class, 'importHistoryDelete'])->name('import-history.delete');

        // Category Export
        Route::get('/category/{category}/export', [App\Http\Controllers\Admin\QuestionBankController::class, 'exportByCategory'])->name('category.export');

        // Import modal
        Route::get('/get-for-import', [App\Http\Controllers\Admin\QuestionBankController::class, 'getForImport'])->name('get-for-import');

        // Core CRUD (keep at bottom to avoid conflicts)
        Route::get('/{questionBank}', [App\Http\Controllers\Admin\QuestionBankController::class, 'show'])->name('show');
        Route::get('/{questionBank}/edit', [App\Http\Controllers\Admin\QuestionBankController::class, 'edit'])->name('edit');
        Route::put('/{questionBank}', [App\Http\Controllers\Admin\QuestionBankController::class, 'update'])->name('update');
        Route::delete('/{questionBank}', [App\Http\Controllers\Admin\QuestionBankController::class, 'destroy'])->name('destroy');
    });

    // Import questions from bank
    Route::post('exams/{exam}/questions/import-from-bank', [App\Http\Controllers\Admin\QuestionController::class, 'importFromBank'])->name('exams.questions.import-from-bank');

    // Forum Category Management (Admin only)
    Route::resource('forum-categories', App\Http\Controllers\Admin\ForumCategoryController::class);

    // School Management
    Route::resource('schools', App\Http\Controllers\Admin\SchoolController::class);
    Route::post('schools/{school}/toggle-active', [App\Http\Controllers\Admin\SchoolController::class, 'toggleActive'])->name('schools.toggle-active');

    // Theme Management
    Route::get('schools/{school}/theme', [App\Http\Controllers\Admin\ThemeController::class, 'edit'])->name('schools.theme.edit');
    Route::put('schools/{school}/theme', [App\Http\Controllers\Admin\ThemeController::class, 'update'])->name('schools.theme.update');
    Route::post('theme/preview', [App\Http\Controllers\Admin\ThemeController::class, 'preview'])->name('theme.preview');
    Route::post('schools/{school}/theme/apply-palette', [App\Http\Controllers\Admin\ThemeController::class, 'applyPalette'])->name('schools.theme.apply-palette');
    Route::post('schools/{school}/theme/reset', [App\Http\Controllers\Admin\ThemeController::class, 'reset'])->name('schools.theme.reset');
    Route::get('schools/{school}/theme/export', [App\Http\Controllers\Admin\ThemeController::class, 'export'])->name('schools.theme.export');
    Route::post('schools/{school}/theme/import', [App\Http\Controllers\Admin\ThemeController::class, 'import'])->name('schools.theme.import');

    // Landing Page Management
    Route::get('schools/{school}/landing-page', [App\Http\Controllers\Admin\LandingPageController::class, 'edit'])->name('landing-page.edit');
    Route::put('schools/{school}/landing-page', [App\Http\Controllers\Admin\LandingPageController::class, 'update'])->name('landing-page.update');
    Route::get('schools/{school}/landing-page/preview', [App\Http\Controllers\Admin\LandingPageController::class, 'preview'])->name('landing-page.preview');
});

// Guru Dashboard & Course Management
Route::get('/guru/dashboard', [App\Http\Controllers\Guru\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:guru'])
    ->name('guru.dashboard');

Route::middleware(['auth', 'verified', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    // Course Management
    Route::resource('courses', App\Http\Controllers\Guru\CourseController::class);
    Route::post('courses/{course}/toggle-status', [App\Http\Controllers\Guru\CourseController::class, 'toggleStatus'])->name('courses.toggle-status');

    // Enrollment Management
    Route::get('courses/{course}/enrollments', [App\Http\Controllers\EnrollmentController::class, 'index'])->name('courses.enrollments');
    Route::post('courses/{course}/enrollments', [App\Http\Controllers\EnrollmentController::class, 'store'])->name('courses.enrollments.store');
    Route::delete('courses/{course}/enrollments/{enrollment}', [App\Http\Controllers\EnrollmentController::class, 'destroy'])->name('courses.enrollments.destroy');
    Route::patch('courses/{course}/enrollments/{enrollment}/status', [App\Http\Controllers\EnrollmentController::class, 'updateStatus'])->name('courses.enrollments.update-status');
    Route::patch('courses/{course}/enrollments/{enrollment}/progress', [App\Http\Controllers\EnrollmentController::class, 'updateProgress'])->name('courses.enrollments.update-progress');

    // Material Management
    Route::resource('courses.materials', App\Http\Controllers\Guru\MaterialController::class);
    Route::post('courses/{course}/materials/{material}/toggle-status', [App\Http\Controllers\Guru\MaterialController::class, 'toggleStatus'])->name('courses.materials.toggle-status');
    Route::post('courses/{course}/materials/reorder', [App\Http\Controllers\Guru\MaterialController::class, 'reorder'])->name('courses.materials.reorder');

    // Exam Management
    Route::resource('exams', App\Http\Controllers\Guru\ExamController::class);
    Route::post('exams/{exam}/toggle-status', [App\Http\Controllers\Guru\ExamController::class, 'toggleStatus'])->name('exams.toggle-status');
    Route::post('exams/{exam}/duplicate', [App\Http\Controllers\Guru\ExamController::class, 'duplicate'])->name('exams.duplicate');
    Route::get('exams/{exam}/results', [App\Http\Controllers\Guru\ExamController::class, 'results'])->name('exams.results');
    Route::get('exams/{exam}/review-essays', [App\Http\Controllers\Guru\ExamController::class, 'reviewEssays'])->name('exams.review-essays');
    Route::post('exams/{exam}/answers/{answer}/grade', [App\Http\Controllers\Guru\ExamController::class, 'gradeEssay'])->name('exams.grade-essay');

    // Question Management
    Route::resource('exams.questions', App\Http\Controllers\Guru\QuestionController::class);
    Route::post('exams/{exam}/questions/reorder', [App\Http\Controllers\Guru\QuestionController::class, 'reorder'])->name('exams.questions.reorder');
    Route::post('exams/{exam}/questions/{question}/duplicate', [App\Http\Controllers\Guru\QuestionController::class, 'duplicate'])->name('exams.questions.duplicate');

    // Report Routes (Guru)
    Route::get('reports', [App\Http\Controllers\Guru\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/exams/{exam}/export-excel', [App\Http\Controllers\Guru\ReportController::class, 'exportGradesExcel'])->name('reports.export-grades-excel');
    Route::get('reports/exams/{exam}/export-pdf', [App\Http\Controllers\Guru\ReportController::class, 'exportGradesPdf'])->name('reports.export-grades-pdf');
    Route::get('reports/courses/{course}/students/{student}/transcript-pdf', [App\Http\Controllers\Guru\ReportController::class, 'exportStudentTranscriptPdf'])->name('reports.student-transcript-pdf');

    // Analytics Routes (Guru)
    Route::get('analytics', [App\Http\Controllers\Guru\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/student-performance-by-course', [App\Http\Controllers\Guru\AnalyticsController::class, 'studentPerformanceByCourse'])->name('analytics.student-performance-by-course');
    Route::get('analytics/exam-completion-rate', [App\Http\Controllers\Guru\AnalyticsController::class, 'examCompletionRate'])->name('analytics.exam-completion-rate');
    Route::get('analytics/grade-distribution', [App\Http\Controllers\Guru\AnalyticsController::class, 'gradeDistribution'])->name('analytics.grade-distribution');
    Route::get('analytics/student-engagement-metrics', [App\Http\Controllers\Guru\AnalyticsController::class, 'studentEngagementMetrics'])->name('analytics.student-engagement-metrics');
});

// Siswa Dashboard & Course Browsing
Route::get('/siswa/dashboard', [App\Http\Controllers\Siswa\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:siswa'])
    ->name('siswa.dashboard');

Route::middleware(['auth', 'verified', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    // Browse Courses
    Route::get('courses', [App\Http\Controllers\Siswa\CourseController::class, 'index'])->name('courses.index');
    Route::get('courses/{course}', [App\Http\Controllers\Siswa\CourseController::class, 'show'])->name('courses.show');

    // My Courses
    Route::get('my-courses', [App\Http\Controllers\Siswa\CourseController::class, 'myCourses'])->name('courses.my-courses');

    // Enrollment Actions
    Route::post('courses/{course}/enroll', [App\Http\Controllers\Siswa\CourseController::class, 'enroll'])->name('courses.enroll');
    Route::post('enroll-by-code', [App\Http\Controllers\Siswa\CourseController::class, 'enrollByCode'])->name('courses.enroll-by-code');
    Route::delete('courses/{course}/unenroll', [App\Http\Controllers\Siswa\CourseController::class, 'unenroll'])->name('courses.unenroll');

    // Exam Routes
    Route::get('exams', [App\Http\Controllers\Siswa\ExamController::class, 'index'])->name('exams.index');
    Route::get('exams/{exam}', [App\Http\Controllers\Siswa\ExamController::class, 'show'])->name('exams.show');
    Route::get('my-attempts', [App\Http\Controllers\Siswa\ExamController::class, 'myAttempts'])->name('exams.my-attempts');
    Route::get('attempts/{attempt}/review', [App\Http\Controllers\Siswa\ExamController::class, 'reviewAttempt'])->name('exams.review-attempt');

    // Exam Taking Routes
    Route::post('exams/{exam}/start', [App\Http\Controllers\ExamAttemptController::class, 'start'])->name('exams.start');
    Route::get('attempts/{attempt}/take', [App\Http\Controllers\ExamAttemptController::class, 'take'])->name('exams.take');
    Route::post('attempts/{attempt}/save-answer', [App\Http\Controllers\ExamAttemptController::class, 'saveAnswer'])->name('exams.save-answer');
    Route::post('attempts/{attempt}/submit', [App\Http\Controllers\ExamAttemptController::class, 'submit'])->name('exams.submit');

    // Anti-Cheat Tracking Routes
    Route::post('attempts/{attempt}/track-tab-switch', [App\Http\Controllers\ExamAttemptController::class, 'trackTabSwitch'])->name('exams.track-tab-switch');
    Route::post('attempts/{attempt}/track-fullscreen-exit', [App\Http\Controllers\ExamAttemptController::class, 'trackFullscreenExit'])->name('exams.track-fullscreen-exit');
    Route::get('attempts/{attempt}/time-remaining', [App\Http\Controllers\ExamAttemptController::class, 'getTimeRemaining'])->name('exams.time-remaining');

    // Report Routes (Siswa)
    Route::get('reports/my-transcript', [App\Http\Controllers\Siswa\ReportController::class, 'myTranscript'])->name('reports.my-transcript');
    Route::get('reports/courses/{course}/transcript-pdf', [App\Http\Controllers\Siswa\ReportController::class, 'exportMyTranscriptPdf'])->name('reports.my-transcript-pdf');

    // Analytics Routes (Siswa)
    Route::get('analytics', [App\Http\Controllers\Siswa\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/performance-trend', [App\Http\Controllers\Siswa\AnalyticsController::class, 'performanceTrend'])->name('analytics.performance-trend');
    Route::get('analytics/performance-by-course', [App\Http\Controllers\Siswa\AnalyticsController::class, 'performanceByCourse'])->name('analytics.performance-by-course');
    Route::get('analytics/exam-pass-fail-ratio', [App\Http\Controllers\Siswa\AnalyticsController::class, 'examPassFailRatio'])->name('analytics.exam-pass-fail-ratio');
    Route::get('analytics/study-time-distribution', [App\Http\Controllers\Siswa\AnalyticsController::class, 'studyTimeDistribution'])->name('analytics.study-time-distribution');
    Route::get('analytics/recent-exam-scores', [App\Http\Controllers\Siswa\AnalyticsController::class, 'recentExamScores'])->name('analytics.recent-exam-scores');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Profile Photo Routes
    Route::post('/profile/photo/upload', [App\Http\Controllers\ProfilePhotoController::class, 'upload'])->name('profile.photo.upload');
    Route::delete('/profile/photo/delete', [App\Http\Controllers\ProfilePhotoController::class, 'delete'])->name('profile.photo.delete');

    // Material Comments (all authenticated users)
    Route::post('materials/{material}/comments', [App\Http\Controllers\MaterialCommentController::class, 'store'])->name('materials.comments.store');
    Route::patch('comments/{comment}', [App\Http\Controllers\MaterialCommentController::class, 'update'])->name('comments.update');
    Route::delete('comments/{comment}', [App\Http\Controllers\MaterialCommentController::class, 'destroy'])->name('comments.destroy');

    // Notifications (all authenticated users)
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [App\Http\Controllers\NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/{id}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::delete('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Forum Routes (all authenticated users)
    Route::prefix('forum')->name('forum.')->group(function () {
        Route::get('/', [App\Http\Controllers\ForumController::class, 'index'])->name('index');
        Route::get('/search', [App\Http\Controllers\ForumController::class, 'search'])->name('search');
        Route::get('/create', [App\Http\Controllers\ForumController::class, 'create'])->name('create');
        Route::post('/store', [App\Http\Controllers\ForumController::class, 'store'])->name('store');
        Route::get('/{category}', [App\Http\Controllers\ForumController::class, 'category'])->name('category');
        Route::get('/{category}/create', [App\Http\Controllers\ForumController::class, 'create'])->name('create-in-category');
        Route::get('/{category}/{thread}', [App\Http\Controllers\ForumController::class, 'show'])->name('thread');
        Route::get('/{category}/{thread}/edit', [App\Http\Controllers\ForumController::class, 'edit'])->name('edit');
        Route::put('/{category}/{thread}', [App\Http\Controllers\ForumController::class, 'update'])->name('update');
        Route::delete('/{category}/{thread}', [App\Http\Controllers\ForumController::class, 'destroy'])->name('destroy');

        // Replies
        Route::post('/{category}/{thread}/reply', [App\Http\Controllers\ForumController::class, 'storeReply'])->name('reply');
        Route::put('/reply/{reply}', [App\Http\Controllers\ForumController::class, 'updateReply'])->name('reply.update');
        Route::delete('/reply/{reply}', [App\Http\Controllers\ForumController::class, 'destroyReply'])->name('reply.destroy');

        // Actions
        Route::post('/like', [App\Http\Controllers\ForumController::class, 'toggleLike'])->name('like');
        Route::post('/{category}/{thread}/pin', [App\Http\Controllers\ForumController::class, 'togglePin'])->name('pin');
        Route::post('/{category}/{thread}/lock', [App\Http\Controllers\ForumController::class, 'toggleLock'])->name('lock');
        Route::post('/reply/{reply}/solution', [App\Http\Controllers\ForumController::class, 'markSolution'])->name('solution');
    });
});

require __DIR__ . '/auth.php';
require __DIR__ . '/certificates.php';
require __DIR__ . '/offline.php';
require __DIR__ . '/ai.php';
