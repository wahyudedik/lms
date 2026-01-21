I will implement the fixes for the issues identified in the audit report:

1.  **Fix Dependencies (`composer.json`)**:
    *   Downgrade `laravel/framework` from `^12.0` (unstable) to `^11.0` (stable).
    *   Lock `maatwebsite/excel` to `^3.1`.
    *   Lock `spatie/laravel-medialibrary` to `^11.0`.

2.  **Fix N+1 Queries**:
    *   **QuestionController**: Refactor `importFromBank` to fetch all `QuestionBank` items in one query using `whereIn` instead of looping with `find`.
    *   **AnalyticsController**: I will inspect `Guru/AnalyticsController` and `Siswa/AnalyticsController` to locate the reported `getStatistics` N+1 loop (since it wasn't in the Admin controller) and fix it if found.

3.  **Implement Rate Limiting**:
    *   Update `routes/web.php` to apply the `throttle` middleware to critical Guest Exam endpoints:
        *   `verify-token`: Limit to 5 attempts per minute (prevent brute force).
        *   `save-answer`: Limit to 60 attempts per minute (prevent spam).

I will verify the changes by checking the code structure after editing.