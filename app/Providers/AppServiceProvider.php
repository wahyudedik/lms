<?php

namespace App\Providers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\Material;
use App\Models\Question;
use App\Policies\CertificatePolicy;
use App\Policies\CoursePolicy;
use App\Policies\EnrollmentPolicy;
use App\Policies\ExamPolicy;
use App\Policies\ForumReplyPolicy;
use App\Policies\ForumThreadPolicy;
use App\Policies\MaterialPolicy;
use App\Policies\QuestionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(Exam::class, ExamPolicy::class);
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Material::class, MaterialPolicy::class);
        Gate::policy(Question::class, QuestionPolicy::class);
        Gate::policy(Enrollment::class, EnrollmentPolicy::class);
        Gate::policy(Certificate::class, CertificatePolicy::class);
        Gate::policy(ForumThread::class, ForumThreadPolicy::class);
        Gate::policy(ForumReply::class, ForumReplyPolicy::class);
    }
}
