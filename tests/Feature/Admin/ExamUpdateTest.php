<?php

use App\Models\Course;
use App\Models\Exam;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('checkbox fields are updated to false when unchecked', function () {
    // 1. Create a user (admin)
    $user = User::factory()->create(['role' => 'admin']);
    $this->actingAs($user);

    // 2. Create a course
    $course = Course::factory()->create();

    // 3. Create an exam with all booleans set to true
    $exam = Exam::factory()->create([
        'course_id' => $course->id,
        'created_by' => $user->id,
        'shuffle_questions' => true,
        'shuffle_options' => true,
        'show_results_immediately' => true,
        'show_correct_answers' => true,
        'require_fullscreen' => true,
        'detect_tab_switch' => true,
    ]);

    // Verify initial state
    expect($exam->shuffle_questions)->toBeTrue();
    expect($exam->shuffle_options)->toBeTrue();

    // 4. Send update request with these fields MISSING (simulating unchecked checkboxes)
    $response = $this->put(route('admin.exams.update', $exam), [
        'course_id' => $course->id,
        'title' => 'Updated Title',
        'duration_minutes' => 60,
        'max_attempts' => 1,
        'pass_score' => 75,
        // Checkboxes are omitted
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    // 5. Refresh exam and assert fields are false
    $exam->refresh();

    expect($exam->shuffle_questions)->toBeFalse();
    expect($exam->shuffle_options)->toBeFalse();
    expect($exam->show_results_immediately)->toBeFalse();
    expect($exam->show_correct_answers)->toBeFalse();
    expect($exam->require_fullscreen)->toBeFalse();
    expect($exam->detect_tab_switch)->toBeFalse();
});
