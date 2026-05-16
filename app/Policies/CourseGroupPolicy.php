<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\User;

class CourseGroupPolicy
{
    /**
     * Determine if the user can view any groups for the course.
     */
    public function viewAny(User $user, Course $course): bool
    {
        return $this->isAuthorizedForCourse($user, $course);
    }

    /**
     * Determine if the user can create groups in the course.
     */
    public function create(User $user, Course $course): bool
    {
        return $this->isAuthorizedForCourse($user, $course);
    }

    /**
     * Determine if the user can update the group.
     */
    public function update(User $user, CourseGroup $group): bool
    {
        return $this->isAuthorizedForCourse($user, $group->course);
    }

    /**
     * Determine if the user can delete the group.
     */
    public function delete(User $user, CourseGroup $group): bool
    {
        return $this->isAuthorizedForCourse($user, $group->course);
    }

    /**
     * Determine if the user can manage members of the group.
     */
    public function manageMember(User $user, CourseGroup $group): bool
    {
        return $this->isAuthorizedForCourse($user, $group->course);
    }

    /**
     * Check if the user is authorized to manage groups for the given course.
     *
     * Admin → always allowed
     * Guru/Dosen → allowed only if course.instructor_id === user.id
     * Siswa/Mahasiswa → always denied
     */
    private function isAuthorizedForCourse(User $user, Course $course): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isGuru() || $user->isDosen()) {
            return $course->instructor_id === $user->id;
        }

        return false;
    }
}
