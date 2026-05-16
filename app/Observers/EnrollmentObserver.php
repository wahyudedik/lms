<?php

namespace App\Observers;

use App\Models\CourseGroup;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class EnrollmentObserver
{
    /**
     * Handle the Enrollment "updated" event.
     *
     * When enrollment status changes to "dropped", remove the user
     * from all course groups in that course within a DB transaction.
     */
    public function updated(Enrollment $enrollment): void
    {
        if ($enrollment->isDirty('status') && $enrollment->status === 'dropped') {
            $this->removeFromAllCourseGroups($enrollment);
        }
    }

    /**
     * Handle the Enrollment "deleted" event.
     *
     * Remove the user from all course groups in that course within a DB transaction.
     */
    public function deleted(Enrollment $enrollment): void
    {
        $this->removeFromAllCourseGroups($enrollment);
    }

    /**
     * Remove the user from all course groups in the enrollment's course.
     */
    protected function removeFromAllCourseGroups(Enrollment $enrollment): void
    {
        DB::transaction(function () use ($enrollment) {
            $groupIds = CourseGroup::where('course_id', $enrollment->course_id)->pluck('id');

            if ($groupIds->isNotEmpty()) {
                DB::table('course_group_members')
                    ->where('user_id', $enrollment->user_id)
                    ->whereIn('course_group_id', $groupIds)
                    ->delete();
            }
        });
    }
}
