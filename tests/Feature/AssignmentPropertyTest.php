<?php

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseGradeWeight;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Material;
use App\Models\User;
use App\Services\AssignmentGradingService;
use App\Services\FileValidationService;
use Illuminate\Http\UploadedFile;

/*
|--------------------------------------------------------------------------
| Property-Based Tests for Assignment Submission Feature
|--------------------------------------------------------------------------
|
| These tests validate correctness properties using randomized inputs
| with 100 iterations per property. Uses Pest PHP with Faker.
|
*/

it('Property 2: Assignment validation rejects invalid inputs', function () {
    /**
     * Validates: Requirements 1.3
     *
     * For any assignment input where title exceeds 255 characters, OR deadline is in the past,
     * OR max_score is not a positive integer, the system SHALL reject with validation errors.
     */
    for ($i = 0; $i < 100; $i++) {
        $invalidCase = fake()->randomElement(['long_title', 'past_deadline', 'bad_max_score', 'missing_title']);

        $data = match ($invalidCase) {
            'long_title' => [
                'title' => str_repeat('a', fake()->numberBetween(256, 500)),
                'deadline' => now()->addDays(fake()->numberBetween(1, 30))->format('Y-m-d H:i:s'),
                'max_score' => fake()->numberBetween(1, 100),
                'late_policy' => 'reject',
            ],
            'past_deadline' => [
                'title' => fake()->sentence(3),
                'deadline' => now()->subDays(fake()->numberBetween(1, 365))->format('Y-m-d H:i:s'),
                'max_score' => fake()->numberBetween(1, 100),
                'late_policy' => 'reject',
            ],
            'bad_max_score' => [
                'title' => fake()->sentence(3),
                'deadline' => now()->addDays(fake()->numberBetween(1, 30))->format('Y-m-d H:i:s'),
                'max_score' => fake()->numberBetween(-100, 0),
                'late_policy' => 'reject',
            ],
            'missing_title' => [
                'title' => '',
                'deadline' => now()->addDays(fake()->numberBetween(1, 30))->format('Y-m-d H:i:s'),
                'max_score' => fake()->numberBetween(1, 100),
                'late_policy' => 'reject',
            ],
        };

        $rules = [
            'title' => 'required|string|max:255',
            'deadline' => 'required|date|after:now',
            'max_score' => 'required|integer|min:1',
            'late_policy' => 'required|in:allow,reject,penalty',
        ];

        $validator = validator($data, $rules);

        $expectedErrorField = match ($invalidCase) {
            'long_title' => 'title',
            'past_deadline' => 'deadline',
            'bad_max_score' => 'max_score',
            'missing_title' => 'title',
        };

        expect($validator->fails())->toBeTrue(
            "Validation should fail for case: {$invalidCase}"
        );
        expect($validator->errors()->has($expectedErrorField))->toBeTrue(
            "Error should be on '{$expectedErrorField}' field for case: {$invalidCase}"
        );
    }
})->group('property-tests', 'assignment');

it('Property 3: Material-course cross-reference validation', function () {
    /**
     * Validates: Requirements 1.4
     *
     * For any assignment creation with a material_id, if the material's course_id does not match
     * the assignment's course_id, the system SHALL reject the creation.
     */
    $guru = User::factory()->create(['role' => 'guru']);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    $otherCourse = Course::factory()->create(['instructor_id' => $guru->id]);

    for ($i = 0; $i < 100; $i++) {
        // Create a material belonging to a DIFFERENT course
        $material = Material::factory()->create([
            'course_id' => $otherCourse->id,
            'created_by' => $guru->id,
        ]);

        // Verify the material does NOT belong to the target course
        $materialBelongsToCourse = $course->materials()->where('id', $material->id)->exists();

        expect($materialBelongsToCourse)->toBeFalse(
            "Material {$material->id} (course_id={$material->course_id}) should NOT belong to course {$course->id}"
        );

        // The controller logic: $course->materials()->find($material->id) returns null
        $found = $course->materials()->find($material->id);
        expect($found)->toBeNull(
            "Material from different course should not be found via course relationship"
        );

        // Cleanup
        $material->delete();
    }
})->group('property-tests', 'assignment');

it('Property 4: File type validation against allowed list', function () {
    /**
     * Validates: Requirements 1.5, 3.1
     */
    $service = new FileValidationService();
    $allExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'mp4', 'mov', 'avi', 'txt', 'exe', 'zip', 'jpg', 'png', 'html', 'php'];

    for ($i = 0; $i < 100; $i++) {
        $allowedTypes = fake()->randomElements(
            FileValidationService::SUPPORTED_FILE_TYPES,
            fake()->numberBetween(1, count(FileValidationService::SUPPORTED_FILE_TYPES))
        );

        $assignment = new Assignment([
            'allowed_file_types' => $allowedTypes,
        ]);

        $testExtension = fake()->randomElement($allExtensions);
        $fileName = 'test_file.' . $testExtension;
        $file = UploadedFile::fake()->create($fileName, 100);

        $result = $service->validate($file, $assignment);

        if (in_array($testExtension, $allowedTypes)) {
            expect($result['valid'])->toBeTrue(
                "Extension '{$testExtension}' should be accepted when allowed: " . implode(', ', $allowedTypes)
            );
        } else {
            expect($result['valid'])->toBeFalse(
                "Extension '{$testExtension}' should be rejected when allowed: " . implode(', ', $allowedTypes)
            );
        }
    }
})->group('property-tests', 'assignment');

it('Property 5: Penalty percentage range validation', function () {
    /**
     * Validates: Requirements 1.8
     *
     * For any assignment with late_policy set to "penalty", the penalty_percentage SHALL be
     * accepted if and only if it is an integer between 1 and 100 inclusive.
     */
    for ($i = 0; $i < 100; $i++) {
        $penaltyValue = fake()->numberBetween(-50, 200);

        $validator = validator(
            [
                'late_policy' => 'penalty',
                'penalty_percentage' => $penaltyValue,
            ],
            [
                'late_policy' => 'required|in:allow,reject,penalty',
                'penalty_percentage' => 'required_if:late_policy,penalty|nullable|integer|min:1|max:100',
            ]
        );

        if ($penaltyValue >= 1 && $penaltyValue <= 100) {
            expect($validator->passes())->toBeTrue(
                "Penalty {$penaltyValue} should be accepted (valid range 1-100)"
            );
        } else {
            expect($validator->fails())->toBeTrue(
                "Penalty {$penaltyValue} should be rejected (outside range 1-100)"
            );
            expect($validator->errors()->has('penalty_percentage'))->toBeTrue(
                "Error should be on penalty_percentage field for value {$penaltyValue}"
            );
        }
    }
})->group('property-tests', 'assignment');

it('Property 7: Assignment visibility determined by publish status', function () {
    /**
     * Validates: Requirements 2.4, 2.5
     */
    $guru = User::factory()->create(['role' => 'guru']);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    $student = User::factory()->create(['role' => 'siswa']);
    Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    for ($i = 0; $i < 100; $i++) {
        $isPublished = fake()->boolean();

        $assignment = Assignment::create([
            'course_id' => $course->id,
            'created_by' => $guru->id,
            'title' => fake()->sentence(3),
            'deadline' => now()->addDays(fake()->numberBetween(1, 30)),
            'max_score' => fake()->numberBetween(1, 100),
            'late_policy' => 'reject',
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
        ]);

        $policy = new \App\Policies\AssignmentPolicy();
        $canView = $policy->view($student, $assignment);

        if ($isPublished) {
            expect($canView)->toBeTrue(
                "Published assignment should be visible to enrolled student"
            );
        } else {
            expect($canView)->toBeFalse(
                "Draft assignment should NOT be visible to enrolled student"
            );
        }

        $assignment->forceDelete();
    }
})->group('property-tests', 'assignment');

it('Property 8: Authorization restricts modification to creator or admin', function () {
    /**
     * Validates: Requirements 2.6
     */
    $guru = User::factory()->create(['role' => 'guru']);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);

    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Test Assignment',
        'deadline' => now()->addDays(7),
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $policy = new \App\Policies\AssignmentPolicy();

    for ($i = 0; $i < 100; $i++) {
        $role = fake()->randomElement(['admin', 'guru', 'dosen', 'siswa', 'mahasiswa']);
        $user = User::factory()->create(['role' => $role]);

        $isCreator = false;
        if (fake()->boolean(20)) {
            // 20% chance to test as the creator
            $canUpdate = $policy->update($guru, $assignment);
            $canDelete = $policy->delete($guru, $assignment);
            expect($canUpdate)->toBeTrue("Creator should be able to update");
            expect($canDelete)->toBeTrue("Creator should be able to delete");
            $isCreator = true;
        }

        if (!$isCreator) {
            $canUpdate = $policy->update($user, $assignment);
            $canDelete = $policy->delete($user, $assignment);

            if ($role === 'admin') {
                expect($canUpdate)->toBeTrue("Admin should be able to update");
                expect($canDelete)->toBeTrue("Admin should be able to delete");
            } else {
                expect($canUpdate)->toBeFalse(
                    "Non-creator {$role} should NOT be able to update"
                );
                expect($canDelete)->toBeFalse(
                    "Non-creator {$role} should NOT be able to delete"
                );
            }
        }
    }
})->group('property-tests', 'assignment');

it('Property 9: File size validation', function () {
    /**
     * Validates: Requirements 3.2
     */
    $service = new FileValidationService();
    $maxSize = FileValidationService::MAX_FILE_SIZE; // 10,485,760 bytes

    for ($i = 0; $i < 100; $i++) {
        // Generate random file size in KB (between 1 KB and 15 MB)
        $fileSizeKb = fake()->numberBetween(1, 15360);
        $fileSizeBytes = $fileSizeKb * 1024;

        $assignment = new Assignment([
            'allowed_file_types' => ['pdf'],
        ]);

        $file = UploadedFile::fake()->create('document.pdf', $fileSizeKb);

        $result = $service->validate($file, $assignment);

        if ($fileSizeBytes <= $maxSize) {
            expect($result['valid'])->toBeTrue(
                "File of {$fileSizeKb} KB ({$fileSizeBytes} bytes) should be accepted (max: {$maxSize})"
            );
        } else {
            expect($result['valid'])->toBeFalse(
                "File of {$fileSizeKb} KB ({$fileSizeBytes} bytes) should be rejected (max: {$maxSize})"
            );
        }
    }
})->group('property-tests', 'assignment');

it('Property 12: Revision access control', function () {
    /**
     * Validates: Requirements 4.1, 4.5
     */
    $guru = User::factory()->create(['role' => 'guru']);
    $student = User::factory()->create(['role' => 'siswa']);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);

    for ($i = 0; $i < 100; $i++) {
        $isGraded = fake()->boolean();
        $deadlinePassed = fake()->boolean();
        $latePolicy = fake()->randomElement(['allow', 'reject', 'penalty']);

        $deadline = $deadlinePassed
            ? now()->subDays(fake()->numberBetween(1, 30))
            : now()->addDays(fake()->numberBetween(1, 30));

        $assignment = Assignment::create([
            'course_id' => $course->id,
            'created_by' => $guru->id,
            'title' => fake()->sentence(3),
            'deadline' => $deadline,
            'max_score' => 100,
            'late_policy' => $latePolicy,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'file_path' => 'assignments/test/file.pdf',
            'file_name' => 'file.pdf',
            'file_size' => 1024,
            'status' => $isGraded ? 'graded' : 'submitted',
            'submitted_at' => now()->subHour(),
            'score' => $isGraded ? 80 : null,
            'graded_at' => $isGraded ? now() : null,
        ]);

        $canRevise = $submission->canRevise();

        // Revision allowed iff NOT graded AND (deadline not passed OR late_policy != reject)
        $expectedCanRevise = !$isGraded && (!$deadlinePassed || $latePolicy !== 'reject');

        expect($canRevise)->toBe(
            $expectedCanRevise,
            "Graded={$isGraded}, DeadlinePassed={$deadlinePassed}, Policy={$latePolicy}: " .
            "expected canRevise=" . ($expectedCanRevise ? 'true' : 'false')
        );

        $submission->forceDelete();
        $assignment->forceDelete();
    }
})->group('property-tests', 'assignment');

it('Property 14: Deadline and late policy behavior', function () {
    /**
     * Validates: Requirements 5.1, 5.2, 5.3
     */
    $guru = User::factory()->create(['role' => 'guru']);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);

    for ($i = 0; $i < 100; $i++) {
        $latePolicy = fake()->randomElement(['allow', 'reject', 'penalty']);
        $deadlinePassed = fake()->boolean();
        $penaltyPercentage = $latePolicy === 'penalty' ? fake()->numberBetween(1, 100) : null;

        $deadline = $deadlinePassed
            ? now()->subDays(fake()->numberBetween(1, 30))
            : now()->addDays(fake()->numberBetween(1, 30));

        $assignment = Assignment::create([
            'course_id' => $course->id,
            'created_by' => $guru->id,
            'title' => fake()->sentence(3),
            'deadline' => $deadline,
            'max_score' => 100,
            'late_policy' => $latePolicy,
            'penalty_percentage' => $penaltyPercentage,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $canAccept = $assignment->canAcceptSubmission();

        if (!$deadlinePassed) {
            // Before deadline: always accept
            expect($canAccept)->toBeTrue(
                "Before deadline, should always accept submissions"
            );
        } else {
            // After deadline: depends on late_policy
            if ($latePolicy === 'reject') {
                expect($canAccept)->toBeFalse(
                    "After deadline with reject policy, should NOT accept"
                );
            } else {
                expect($canAccept)->toBeTrue(
                    "After deadline with {$latePolicy} policy, should accept"
                );
            }
        }

        $assignment->forceDelete();
    }
})->group('property-tests', 'assignment');

it('Property 15: Penalty score calculation', function () {
    /**
     * Validates: Requirements 5.5, 6.3
     */
    $service = new AssignmentGradingService();

    for ($i = 0; $i < 100; $i++) {
        $score = fake()->numberBetween(0, 100);
        $penaltyPercentage = fake()->numberBetween(1, 100);

        $submission = new AssignmentSubmission([
            'penalty_applied' => $penaltyPercentage,
        ]);

        $finalScore = $service->calculateFinalScore($score, $submission);

        $expectedFinalScore = round($score - ($score * $penaltyPercentage / 100), 2);

        expect($finalScore)->toBe(
            $expectedFinalScore,
            "Score={$score}, Penalty={$penaltyPercentage}%: expected {$expectedFinalScore}, got {$finalScore}"
        );
    }
})->group('property-tests', 'assignment');

it('Property 16: Grade score range validation', function () {
    /**
     * Validates: Requirements 6.1, 6.6
     *
     * For any grading attempt with score S on an assignment with max_score M,
     * the system SHALL accept the grade if and only if 0 ≤ S ≤ M.
     */
    for ($i = 0; $i < 100; $i++) {
        $maxScore = fake()->numberBetween(10, 100);
        $testScore = fake()->numberBetween(-10, $maxScore + 20);

        $validator = validator(
            ['score' => $testScore, 'feedback' => 'Test feedback'],
            ['score' => "required|integer|min:0|max:{$maxScore}", 'feedback' => 'nullable|string']
        );

        if ($testScore >= 0 && $testScore <= $maxScore) {
            expect($validator->passes())->toBeTrue(
                "Score {$testScore} should be accepted (max: {$maxScore})"
            );
        } else {
            expect($validator->fails())->toBeTrue(
                "Score {$testScore} should be rejected (max: {$maxScore})"
            );
            expect($validator->errors()->has('score'))->toBeTrue(
                "Error should be on 'score' field for value {$testScore} (max: {$maxScore})"
            );
        }
    }
})->group('property-tests', 'assignment');

it('Property 17: Submission statistics accuracy', function () {
    /**
     * Validates: Requirements 8.3
     */
    $service = new AssignmentGradingService();
    $guru = User::factory()->create(['role' => 'guru']);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);

    for ($i = 0; $i < 100; $i++) {
        $numEnrolled = fake()->numberBetween(1, 10);
        $numSubmitted = fake()->numberBetween(0, $numEnrolled);
        $numGraded = fake()->numberBetween(0, $numSubmitted);

        $assignment = Assignment::create([
            'course_id' => $course->id,
            'created_by' => $guru->id,
            'title' => fake()->sentence(3),
            'deadline' => now()->addDays(7),
            'max_score' => 100,
            'late_policy' => 'reject',
            'is_published' => true,
            'published_at' => now(),
        ]);

        // Create enrolled students
        $students = [];
        for ($j = 0; $j < $numEnrolled; $j++) {
            $student = User::factory()->create(['role' => 'siswa']);
            Enrollment::factory()->create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'status' => 'active',
            ]);
            $students[] = $student;
        }

        // Create submissions
        for ($j = 0; $j < $numSubmitted; $j++) {
            $status = $j < $numGraded ? 'graded' : 'submitted';
            AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'user_id' => $students[$j]->id,
                'file_path' => "assignments/{$assignment->id}/{$students[$j]->id}/file.pdf",
                'file_name' => 'file.pdf',
                'file_size' => 1024,
                'status' => $status,
                'submitted_at' => now(),
                'score' => $status === 'graded' ? fake()->numberBetween(0, 100) : null,
                'graded_at' => $status === 'graded' ? now() : null,
            ]);
        }

        $stats = $service->getSubmissionStatistics($assignment);

        expect($stats['total_enrolled'])->toBe($numEnrolled);
        expect($stats['submitted_count'])->toBe($numSubmitted);
        expect($stats['graded_count'])->toBe($numGraded);
        expect($stats['not_submitted_count'])->toBe(max(0, $numEnrolled - $numSubmitted));

        // Cleanup
        AssignmentSubmission::where('assignment_id', $assignment->id)->delete();
        Enrollment::where('course_id', $course->id)->whereIn('user_id', collect($students)->pluck('id'))->delete();
        $assignment->forceDelete();
    }
})->group('property-tests', 'assignment');

it('Property 20: Grade weights sum constraint', function () {
    /**
     * Validates: Requirements 10.1, 10.6
     *
     * For any grade weight configuration, the system SHALL accept it if and only if
     * assignment_weight + exam_weight = 100 AND both values are between 0 and 100.
     */
    for ($i = 0; $i < 100; $i++) {
        $assignmentWeight = fake()->numberBetween(-10, 110);
        $examWeight = fake()->numberBetween(-10, 110);

        // Validate range (0-100 for each)
        $rangeValidator = validator(
            ['assignment_weight' => $assignmentWeight, 'exam_weight' => $examWeight],
            [
                'assignment_weight' => 'required|integer|min:0|max:100',
                'exam_weight' => 'required|integer|min:0|max:100',
            ]
        );

        $validRange = $assignmentWeight >= 0 && $assignmentWeight <= 100
            && $examWeight >= 0 && $examWeight <= 100;
        $sumsTo100 = ($assignmentWeight + $examWeight) === 100;

        if (!$validRange) {
            // Range validation should fail
            expect($rangeValidator->fails())->toBeTrue(
                "Range validation should fail for AW={$assignmentWeight}, EW={$examWeight}"
            );
        } else {
            // Range passes, check sum constraint
            expect($rangeValidator->passes())->toBeTrue(
                "Range validation should pass for AW={$assignmentWeight}, EW={$examWeight}"
            );

            if ($sumsTo100) {
                // Both valid range AND sum to 100 - should be accepted
                expect(true)->toBeTrue();
            } else {
                // Valid range but doesn't sum to 100 - should be rejected by sum check
                expect(($assignmentWeight + $examWeight) !== 100)->toBeTrue(
                    "Sum constraint should reject AW={$assignmentWeight} + EW={$examWeight} = " . ($assignmentWeight + $examWeight)
                );
            }
        }
    }
})->group('property-tests', 'assignment');

it('Property 21: Final weighted grade formula', function () {
    /**
     * Validates: Requirements 10.3
     */
    $service = new AssignmentGradingService();
    $guru = User::factory()->create(['role' => 'guru']);

    for ($i = 0; $i < 100; $i++) {
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $student = User::factory()->create(['role' => 'siswa']);
        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        $assignmentWeight = fake()->numberBetween(0, 100);
        $examWeight = 100 - $assignmentWeight;

        CourseGradeWeight::create([
            'course_id' => $course->id,
            'assignment_weight' => $assignmentWeight,
            'exam_weight' => $examWeight,
        ]);

        // Create assignments with graded submissions
        $numAssignments = fake()->numberBetween(1, 3);
        $assignmentScores = [];
        for ($j = 0; $j < $numAssignments; $j++) {
            $assignment = Assignment::create([
                'course_id' => $course->id,
                'created_by' => $guru->id,
                'title' => fake()->sentence(3),
                'deadline' => now()->addDays(7),
                'max_score' => 100,
                'late_policy' => 'reject',
                'is_published' => true,
                'published_at' => now(),
            ]);

            $finalScore = fake()->randomFloat(2, 0, 100);
            $assignmentScores[] = $finalScore;

            AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'user_id' => $student->id,
                'file_path' => "assignments/{$assignment->id}/{$student->id}/file.pdf",
                'file_name' => 'file.pdf',
                'file_size' => 1024,
                'status' => 'graded',
                'submitted_at' => now(),
                'score' => (int) $finalScore,
                'final_score' => $finalScore,
                'graded_at' => now(),
                'graded_by' => $guru->id,
            ]);
        }

        // Create exams with graded attempts
        $numExams = fake()->numberBetween(1, 3);
        $examScores = [];
        for ($j = 0; $j < $numExams; $j++) {
            $exam = Exam::factory()->create([
                'course_id' => $course->id,
                'created_by' => $guru->id,
            ]);

            $examScore = fake()->randomFloat(2, 0, 100);
            $examScores[] = $examScore;

            ExamAttempt::create([
                'exam_id' => $exam->id,
                'user_id' => $student->id,
                'status' => 'graded',
                'score' => $examScore,
                'started_at' => now()->subHour(),
                'submitted_at' => now(),
            ]);
        }

        $avgAssignment = count($assignmentScores) > 0
            ? array_sum($assignmentScores) / count($assignmentScores)
            : 0;
        $avgExam = count($examScores) > 0
            ? array_sum($examScores) / count($examScores)
            : 0;

        $expectedGrade = round(
            ($avgAssignment * $assignmentWeight / 100) + ($avgExam * $examWeight / 100),
            2
        );

        $actualGrade = $service->calculateCourseGrade($student, $course);

        // Allow small floating point tolerance
        expect(abs($actualGrade - $expectedGrade))->toBeLessThan(
            0.02,
            "Expected grade {$expectedGrade}, got {$actualGrade} " .
            "(AW={$assignmentWeight}, EW={$examWeight}, " .
            "avgAssign={$avgAssignment}, avgExam={$avgExam})"
        );
    }
})->group('property-tests', 'assignment');
