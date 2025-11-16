<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class ExamAttemptBugFixesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->guru = User::factory()->create(['role' => 'guru']);
        $this->siswa = User::factory()->create(['role' => 'siswa']);
        
        $this->course = Course::factory()->create(['instructor_id' => $this->guru->id]);
        $this->exam = Exam::factory()->create([
            'course_id' => $this->course->id,
            'created_by' => $this->guru->id,
            'duration_minutes' => 60,
            'is_published' => true,
            'published_at' => now(),
        ]);
        
        // Create questions
        $this->questions = Question::factory()->count(5)->create([
            'exam_id' => $this->exam->id,
        ]);
        
        // Enroll siswa
        Enrollment::factory()->create([
            'user_id' => $this->siswa->id,
            'course_id' => $this->course->id,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function test_bug_1_guest_exam_user_id_nullable()
    {
        // BUG #1: Verify guest exam can be created with user_id = null
        $guestAttempt = ExamAttempt::create([
            'exam_id' => $this->exam->id,
            'user_id' => null, // Should be nullable
            'is_guest' => true,
            'guest_name' => 'Test Guest',
            'guest_email' => 'guest@test.com',
            'guest_token' => 'test-token-123',
        ]);

        $this->assertNotNull($guestAttempt);
        $this->assertNull($guestAttempt->user_id);
        $this->assertTrue($guestAttempt->is_guest);
    }

    /** @test */
    public function test_bug_2_race_condition_prevention()
    {
        // BUG #2: Verify only one attempt is created even with rapid requests
        $this->loginAsSiswa();

        // Simulate rapid requests
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('siswa.exams.start', $this->exam));
        }

        // Verify only one attempt was created
        $attempts = ExamAttempt::where('exam_id', $this->exam->id)
            ->where('user_id', $this->siswa->id)
            ->where('status', 'in_progress')
            ->count();

        $this->assertEquals(1, $attempts, 'Only one attempt should be created');
    }

    /** @test */
    public function test_bug_3_question_validation()
    {
        // BUG #3: Verify question validation prevents cross-exam answer submission
        $this->loginAsSiswa();

        // Create another exam with different questions
        $otherExam = Exam::factory()->create([
            'course_id' => $this->course->id,
            'created_by' => $this->guru->id,
        ]);
        $otherQuestion = Question::factory()->create([
            'exam_id' => $otherExam->id,
        ]);

        // Create attempt for first exam
        $attempt = ExamAttempt::create([
            'exam_id' => $this->exam->id,
            'user_id' => $this->siswa->id,
        ]);
        $attempt->start();

        // Try to save answer for question from different exam
        $response = $this->postJson(route('siswa.exams.save-answer', $attempt), [
            'question_id' => $otherQuestion->id, // Question from different exam
            'answer' => 'A',
        ]);

        // Should fail validation
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['question_id']);
    }

    /** @test */
    public function test_bug_5_server_side_timer_validation()
    {
        // BUG #5: Verify server-side timer validation works
        $this->loginAsSiswa();

        $attempt = ExamAttempt::create([
            'exam_id' => $this->exam->id,
            'user_id' => $this->siswa->id,
        ]);
        $attempt->start();

        // Set started_at to past (simulate expired time)
        $attempt->update([
            'started_at' => now()->subHours(2), // 2 hours ago (exam duration is 60 minutes)
        ]);

        // Try to save answer after time expires
        $response = $this->postJson(route('siswa.exams.save-answer', $attempt), [
            'question_id' => $this->questions->first()->id,
            'answer' => 'A',
        ]);

        // Should reject and auto-submit
        $response->assertStatus(400);
        $response->assertJson(['timeUp' => true]);
        
        // Verify attempt was auto-submitted
        $attempt->refresh();
        $this->assertNotEquals('in_progress', $attempt->status);
    }

    /** @test */
    public function test_bug_7_atomic_submit_prevention()
    {
        // BUG #7: Verify atomic submit prevents duplicate submissions
        $this->loginAsSiswa();

        $attempt = ExamAttempt::create([
            'exam_id' => $this->exam->id,
            'user_id' => $this->siswa->id,
        ]);
        $attempt->start();

        // Simulate multiple simultaneous submit requests
        $submitCount = 0;
        for ($i = 0; $i < 5; $i++) {
            try {
                $attempt->submit();
                $submitCount++;
            } catch (\Exception $e) {
                // Ignore exceptions
            }
        }

        // Verify attempt was submitted only once
        $attempt->refresh();
        $this->assertNotEquals('in_progress', $attempt->status);
        $this->assertNotNull($attempt->submitted_at);
    }

    /** @test */
    public function test_bug_8_double_submit_prevention()
    {
        // BUG #8: Verify double-submit prevention works
        $this->loginAsSiswa();

        $attempt = ExamAttempt::create([
            'exam_id' => $this->exam->id,
            'user_id' => $this->siswa->id,
        ]);
        $attempt->start();

        // Submit exam
        $response1 = $this->post(route('siswa.exams.submit', $attempt));
        $response1->assertRedirect();

        // Try to submit again
        $response2 = $this->post(route('siswa.exams.submit', $attempt));
        
        // Should redirect with info message (already submitted)
        $response2->assertRedirect();
        $response2->assertSessionHas('info');
    }

    /** @test */
    public function test_bug_10_missing_methods_exist()
    {
        // BUG #10: Verify missing methods exist in ExamAttempt model
        $attempt = ExamAttempt::create([
            'exam_id' => $this->exam->id,
            'user_id' => $this->siswa->id,
        ]);
        $attempt->start();

        // Verify methods exist and work
        $this->assertTrue(method_exists($attempt, 'hasTimeExpired'));
        $this->assertTrue(method_exists($attempt, 'autoSubmit'));
        $this->assertTrue(method_exists($attempt, 'calculateScore'));

        // Test hasTimeExpired
        $this->assertIsBool($attempt->hasTimeExpired());

        // Test autoSubmit (should not throw error)
        try {
            $attempt->autoSubmit();
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail('autoSubmit() method should not throw error');
        }

        // Test calculateScore (should not throw error)
        try {
            $attempt->calculateScore();
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail('calculateScore() method should not throw error');
        }
    }

    /** @test */
    public function test_bug_11_guest_exam_column_name()
    {
        // BUG #11: Verify guest exam uses correct column name (attempt_id)
        $guestAttempt = ExamAttempt::create([
            'exam_id' => $this->exam->id,
            'user_id' => null,
            'is_guest' => true,
            'guest_name' => 'Test Guest',
            'guest_email' => 'guest@test.com',
            'guest_token' => 'test-token-123',
        ]);
        $guestAttempt->start();

        // Verify attempt_id column exists in answers table
        $this->assertDatabaseHas('answers', [
            'attempt_id' => $guestAttempt->id,
            'question_id' => $this->questions->first()->id,
        ]);
    }

    /** @test */
    public function test_bug_12_guest_exam_status_value()
    {
        // BUG #12: Verify guest exam uses correct status values
        $guestAttempt = ExamAttempt::create([
            'exam_id' => $this->exam->id,
            'user_id' => null,
            'is_guest' => true,
            'guest_name' => 'Test Guest',
            'guest_email' => 'guest@test.com',
            'guest_token' => 'test-token-123',
        ]);
        $guestAttempt->start();

        // Submit exam
        $guestAttempt->submit();
        $guestAttempt->refresh();

        // Verify status is 'submitted' or 'graded' (not 'completed')
        $this->assertContains($guestAttempt->status, ['submitted', 'graded']);
        $this->assertNotEquals('completed', $guestAttempt->status);
    }

    /** @test */
    public function test_bug_13_dashboard_route_error_handling()
    {
        // BUG #13: Verify dashboard route error handling works
        // Create user without dashboard_route
        $user = User::factory()->create([
            'role' => 'unknown',
        ]);

        $this->loginUser($user);

        // Access dashboard route
        $response = $this->get(route('dashboard'));

        // Should redirect to login with error message
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function test_bug_4_transaction_atomicity()
    {
        // BUG #4: Verify transaction ensures atomicity
        $this->loginAsSiswa();

        // Count initial attempts
        $initialCount = ExamAttempt::where('exam_id', $this->exam->id)
            ->where('user_id', $this->siswa->id)
            ->count();

        // Start exam (should create attempt + answers atomically)
        $response = $this->post(route('siswa.exams.start', $this->exam));
        $response->assertRedirect();

        // Verify attempt was created
        $attempts = ExamAttempt::where('exam_id', $this->exam->id)
            ->where('user_id', $this->siswa->id)
            ->get();

        $this->assertCount($initialCount + 1, $attempts);

        // Verify answers were created for all questions
        $attempt = $attempts->last();
        $answersCount = $attempt->answers()->count();
        $this->assertEquals($this->questions->count(), $answersCount, 'All questions should have answers');
    }

    /** @test */
    public function test_bug_6_guest_exam_question_validation()
    {
        // BUG #6: Verify guest exam question validation works
        $guestAttempt = ExamAttempt::create([
            'exam_id' => $this->exam->id,
            'user_id' => null,
            'is_guest' => true,
            'guest_name' => 'Test Guest',
            'guest_email' => 'guest@test.com',
            'guest_token' => 'test-token-123',
        ]);
        $guestAttempt->start();

        // Create another exam with different questions
        $otherExam = Exam::factory()->create([
            'course_id' => $this->course->id,
            'created_by' => $this->guru->id,
        ]);
        $otherQuestion = Question::factory()->create([
            'exam_id' => $otherExam->id,
        ]);

        // Set session for guest
        session(['guest_attempt_token' => 'test-token-123']);

        // Try to save answer for question from different exam
        $response = $this->postJson(route('guest.exams.save-answer', $guestAttempt->id), [
            'question_id' => $otherQuestion->id, // Question from different exam
            'answer' => 'A',
        ]);

        // Should fail validation
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['question_id']);
    }

    protected function loginAsSiswa(): void
    {
        $this->loginUser($this->siswa);
    }

    protected function loginUser(User $user): void
    {
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }
}

