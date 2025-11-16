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
use App\Models\Setting;
use App\Policies\CertificatePolicy;
use App\Policies\CoursePolicy;
use App\Policies\EnrollmentPolicy;
use App\Policies\ExamPolicy;
use App\Policies\ForumReplyPolicy;
use App\Policies\ForumThreadPolicy;
use App\Policies\MaterialPolicy;
use App\Policies\QuestionPolicy;
use DateTimeZone;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
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
        $this->applyRuntimePreferences();
        $this->ensureValidTimezone();

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

    /**
     * Normalize and apply the configured timezone even if the .env value uses different casing.
     */
    protected function ensureValidTimezone(): void
    {
        $timezone = config('app.timezone');

        if (!$timezone) {
            return;
        }

        try {
            new DateTimeZone($timezone);
            date_default_timezone_set($timezone);
            return;
        } catch (\Exception $e) {
            $normalized = $this->normalizeTimezone($timezone);

            try {
                new DateTimeZone($normalized);
                config(['app.timezone' => $normalized]);
                date_default_timezone_set($normalized);
            } catch (\Exception $inner) {
                config(['app.timezone' => 'UTC']);
                date_default_timezone_set('UTC');
            }
        }
    }

    /**
     * Attempt to correct casing/format issues for timezone strings.
     */
    protected function normalizeTimezone(string $timezone): string
    {
        $segments = explode('/', str_replace(' ', '', $timezone));

        $segments = array_map(function ($segment) {
            $segment = str_replace(['-', '_'], ' ', strtolower($segment));
            return str_replace(' ', '_', ucwords($segment));
        }, $segments);

        return implode('/', $segments);
    }

    /**
     * Apply timezone and locale preferences stored in settings.
     */
    protected function applyRuntimePreferences(): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        $timezone = Setting::get('app_timezone');
        $locale = Setting::get('app_locale');

        if ($timezone) {
            config(['app.timezone' => $timezone]);
        }

        if ($locale) {
            config(['app.locale' => $locale]);
            App::setLocale($locale);
        }
    }
}
