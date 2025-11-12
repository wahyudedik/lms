<?php

namespace App\Http\Controllers\Concerns;

/**
 * Trait untuk common authorization logic
 * 
 * Trait ini menyediakan helper methods untuk authorization checks
 * yang sering digunakan di berbagai controller.
 */
trait AuthorizesResources
{
    /**
     * Authorize resource access dengan policy
     * 
     * @param string $ability
     * @param mixed $resource
     * @return void
     */
    protected function authorizeResource(string $ability, $resource): void
    {
        $this->authorize($ability, $resource);
    }

    /**
     * Authorize multiple resources
     * 
     * @param array $authorizations Array of ['ability' => 'view', 'resource' => $resource]
     * @return void
     */
    protected function authorizeMultiple(array $authorizations): void
    {
        foreach ($authorizations as $auth) {
            $this->authorize($auth['ability'], $auth['resource']);
        }
    }

    /**
     * Check if user can perform action on resource
     * 
     * @param string $ability
     * @param mixed $resource
     * @return bool
     */
    protected function canPerform(string $ability, $resource): bool
    {
        return auth()->user() && auth()->user()->can($ability, $resource);
    }

    /**
     * Authorize course access for guru
     * Helper method untuk check course ownership
     * 
     * @param \App\Models\Course $course
     * @return void
     */
    protected function authorizeCourseAccess($course): void
    {
        $this->authorize('view', $course);
    }

    /**
     * Authorize exam access for guru
     * Helper method untuk check exam ownership through course
     * 
     * @param \App\Models\Exam $exam
     * @return void
     */
    protected function authorizeExamAccess($exam): void
    {
        $this->authorize('view', $exam);
    }

    /**
     * Authorize material access for guru
     * Helper method untuk check material ownership through course
     * 
     * @param \App\Models\Material $material
     * @return void
     */
    protected function authorizeMaterialAccess($material): void
    {
        $this->authorize('view', $material);
    }

    /**
     * Authorize question access for guru
     * Helper method untuk check question ownership through exam
     * 
     * @param \App\Models\Question $question
     * @return void
     */
    protected function authorizeQuestionAccess($question): void
    {
        $this->authorize('view', $question);
    }
}

