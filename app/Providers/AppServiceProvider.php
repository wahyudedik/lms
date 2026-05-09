<?php

namespace App\Providers;

use App\Models\Assignment;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\Material;
use App\Models\Question;
use App\Models\Setting;
use App\Models\AuthorizationLog;
use App\Policies\AssignmentPolicy;
use App\Policies\CertificatePolicy;
use App\Policies\CoursePolicy;
use App\Policies\EnrollmentPolicy;
use App\Policies\ExamPolicy;
use App\Policies\ForumReplyPolicy;
use App\Policies\ForumThreadPolicy;
use App\Policies\MaterialPolicy;
use App\Policies\QuestionPolicy;
use App\Channels\WebPushChannel;
use App\Services\MentionParser;
use DateTimeZone;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
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
        $this->registerBladeDirectives();

        // Register custom notification channels
        Notification::extend('push', fn ($app) => $app->make(WebPushChannel::class));
        $this->app->bind(MentionParser::class, MentionParser::class);

        // Register policies
        Gate::policy(Assignment::class, AssignmentPolicy::class);
        Gate::policy(Exam::class, ExamPolicy::class);
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Material::class, MaterialPolicy::class);
        Gate::policy(Question::class, QuestionPolicy::class);
        Gate::policy(Enrollment::class, EnrollmentPolicy::class);
        Gate::policy(Certificate::class, CertificatePolicy::class);
        Gate::policy(ForumThread::class, ForumThreadPolicy::class);
        Gate::policy(ForumReply::class, ForumReplyPolicy::class);

        // Log all authorization checks to authorization_logs table
        Gate::after(function ($user, $ability, $result, $arguments) {
            // $result is null when no policy handled the check — skip those
            if ($result === null) {
                return;
            }

            try {
                $resource = $arguments[0] ?? null;
                $resourceType = is_object($resource) ? get_class($resource) : (is_string($resource) ? $resource : 'unknown');
                $resourceId = (is_object($resource) && isset($resource->id)) ? $resource->id : null;

                AuthorizationLog::log(
                    userId: $user?->id,
                    resourceType: $resourceType,
                    resourceId: $resourceId,
                    action: $ability,
                    ability: $ability,
                    result: $result ? 'allowed' : 'denied',
                    reason: $result ? null : "Policy denied: {$ability}",
                    metadata: [
                        'route' => request()->route()?->getName(),
                        'method' => request()->method(),
                        'url' => request()->url(),
                    ]
                );
            } catch (\Throwable $e) {
                // Never let logging break the request
            }
        });
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

    /**
     * Register custom Blade directives for role-aware routing.
     */
    protected function registerBladeDirectives(): void
    {
        // @roleRoute('courses.index') - generates route with user's role prefix
        \Illuminate\Support\Facades\Blade::directive('roleRoute', function ($expression) {
            return "<?php echo route(auth()->user()->getRolePrefix() . '.' . {$expression}); ?>";
        });
    }
}
