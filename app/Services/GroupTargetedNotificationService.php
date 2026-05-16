<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Material;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Collection;

class GroupTargetedNotificationService
{
    /**
     * Get the notification recipients for a published material.
     *
     * If the material has group associations, only members of those groups
     * with active enrollment are returned. If no associations, all actively
     * enrolled students are returned.
     *
     * Recipients are filtered by their NotificationPreference for the
     * 'material_published' notification type.
     */
    public function getRecipientsForMaterial(Material $material): Collection
    {
        return $this->getRecipients(
            $material->course_id,
            $material->courseGroups()->pluck('course_groups.id'),
            'material_published'
        );
    }

    /**
     * Get the notification recipients for a published assignment.
     *
     * If the assignment has group associations, only members of those groups
     * with active enrollment are returned. If no associations, all actively
     * enrolled students are returned.
     *
     * Recipients are filtered by their NotificationPreference for the
     * 'assignment_published' notification type.
     */
    public function getRecipientsForAssignment(Assignment $assignment): Collection
    {
        return $this->getRecipients(
            $assignment->course_id,
            $assignment->courseGroups()->pluck('course_groups.id'),
            'assignment_published'
        );
    }

    /**
     * Get recipients based on group targeting and notification preferences.
     *
     * @param int $courseId The course ID
     * @param Collection $groupIds The associated group IDs (empty = ungrouped)
     * @param string $notificationType The notification type key for preference lookup
     */
    protected function getRecipients(int $courseId, Collection $groupIds, string $notificationType): Collection
    {
        $studentRoles = ['siswa', 'mahasiswa'];

        if ($groupIds->isEmpty()) {
            // No group associations: all actively enrolled students
            $recipients = User::whereIn('role', $studentRoles)
                ->whereHas('enrollments', function ($query) use ($courseId) {
                    $query->where('course_id', $courseId)
                        ->where('status', 'active');
                })
                ->get();
        } else {
            // Has group associations: only members of associated groups with active enrollment
            $recipients = User::whereIn('role', $studentRoles)
                ->whereHas('enrollments', function ($query) use ($courseId) {
                    $query->where('course_id', $courseId)
                        ->where('status', 'active');
                })
                ->whereExists(function ($query) use ($groupIds) {
                    $query->select(\Illuminate\Support\Facades\DB::raw(1))
                        ->from('course_group_members')
                        ->whereColumn('course_group_members.user_id', 'users.id')
                        ->whereIn('course_group_members.course_group_id', $groupIds);
                })
                ->get();
        }

        return $this->filterByNotificationPreferences($recipients, $notificationType);
    }

    /**
     * Filter recipients based on their notification preferences.
     *
     * A recipient is included if they have at least one active channel
     * (via_database or via_push) for the given notification type.
     * If no preference record exists, defaults are used (both channels enabled).
     */
    protected function filterByNotificationPreferences(Collection $recipients, string $notificationType): Collection
    {
        return $recipients->filter(function (User $user) use ($notificationType) {
            $preference = NotificationPreference::getForUser($user->id, $notificationType);

            // Include the user if at least one channel is enabled
            return $preference->via_database || $preference->via_push;
        });
    }
}
