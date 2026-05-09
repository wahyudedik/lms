<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Enrollment;
use App\Notifications\AssignmentDeadlineReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendAssignmentDeadlineReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assignments:send-deadline-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications for assignments due within 24 hours';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for assignments with upcoming deadlines...');

        // Find published assignments with deadline within the next 24 hours
        $assignments = Assignment::published()
            ->where('deadline', '>', now())
            ->where('deadline', '<=', now()->addHours(24))
            ->with(['course', 'submissions'])
            ->get();

        if ($assignments->isEmpty()) {
            $this->info('No assignments with upcoming deadlines found.');

            return self::SUCCESS;
        }

        $this->info("Found {$assignments->count()} assignment(s) with deadlines within 24 hours.");

        $totalRemindersSent = 0;

        foreach ($assignments as $assignment) {
            $remindersSent = $this->processAssignment($assignment);
            $totalRemindersSent += $remindersSent;
        }

        $this->info("Done! Sent {$totalRemindersSent} reminder(s) in total.");

        return self::SUCCESS;
    }

    /**
     * Process a single assignment and send reminders to students who haven't submitted.
     */
    private function processAssignment(Assignment $assignment): int
    {
        // Get user IDs who already have a submission for this assignment
        $submittedUserIds = $assignment->submissions->pluck('user_id')->toArray();

        // Find enrolled students (active enrollment) who don't have a submission
        $enrolledStudents = Enrollment::where('course_id', $assignment->course_id)
            ->active()
            ->whereNotIn('user_id', $submittedUserIds)
            ->with('user')
            ->get();

        if ($enrolledStudents->isEmpty()) {
            return 0;
        }

        $remindersSent = 0;

        foreach ($enrolledStudents as $enrollment) {
            $student = $enrollment->user;

            // Check if we already sent this reminder today to avoid duplicates
            if ($this->alreadyNotifiedToday($student, $assignment)) {
                continue;
            }

            $student->notify(new AssignmentDeadlineReminder($assignment));
            $remindersSent++;
        }

        if ($remindersSent > 0) {
            $this->line("  - \"{$assignment->title}\": sent {$remindersSent} reminder(s)");
        }

        return $remindersSent;
    }

    /**
     * Check if the student has already received a deadline reminder for this assignment today.
     */
    private function alreadyNotifiedToday($student, Assignment $assignment): bool
    {
        return DB::table('notifications')
            ->where('notifiable_type', get_class($student))
            ->where('notifiable_id', $student->id)
            ->where('type', AssignmentDeadlineReminder::class)
            ->whereDate('created_at', today())
            ->where('data->assignment_id', $assignment->id)
            ->exists();
    }
}
