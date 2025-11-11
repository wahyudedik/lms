<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎓 Seeding certificates...');

        // First, ensure we have some completed enrollments to work with
        $this->ensureCompletedEnrollments();

        // Get all completed enrollments that don't have certificates yet
        $completedEnrollments = Enrollment::completed()
            ->doesntHave('certificate')
            ->with(['user', 'course'])
            ->get();

        if ($completedEnrollments->isEmpty()) {
            $this->command->warn('⚠️  No completed enrollments found. Creating some...');
            $this->createCompletedEnrollments();

            $completedEnrollments = Enrollment::completed()
                ->doesntHave('certificate')
                ->with(['user', 'course'])
                ->get();
        }

        $this->command->info("Found {$completedEnrollments->count()} completed enrollments");

        // Statistics
        $stats = [
            'total' => 0,
            'excellence' => 0,
            'regular' => 0,
            'revoked' => 0,
            'recent' => 0,
            'old' => 0,
        ];

        // Generate certificates for various scenarios

        // 1. Generate excellent certificates (A grade) - 20%
        $excellentCount = (int) ($completedEnrollments->count() * 0.2);
        if ($excellentCount > 0) {
            $this->command->info("Creating {$excellentCount} excellent certificates (Grade A)...");

            foreach ($completedEnrollments->take($excellentCount) as $enrollment) {
                $this->createCertificate($enrollment, [
                    'final_score' => fake()->numberBetween(90, 100),
                    'grade' => 'A',
                    'metadata' => [
                        'achievement' => 'Excellence Award',
                        'honors' => 'With Distinction',
                        'duration_days' => rand(21, 60),
                        'course_level' => $enrollment->course->level ?? 'advanced',
                        'total_hours' => rand(30, 80),
                        'completion_rate' => 100,
                    ],
                ]);
                $stats['excellence']++;
            }
        }

        // 2. Generate regular certificates (B-C grade) - 60%
        $regularCount = (int) ($completedEnrollments->count() * 0.6);
        if ($regularCount > 0) {
            $this->command->info("Creating {$regularCount} regular certificates (Grade B-C)...");

            foreach ($completedEnrollments->skip($excellentCount)->take($regularCount) as $enrollment) {
                $score = fake()->numberBetween(70, 89);
                $grade = $score >= 80 ? 'B' : 'C';

                $this->createCertificate($enrollment, [
                    'final_score' => $score,
                    'grade' => $grade,
                    'metadata' => [
                        'duration_days' => rand(14, 90),
                        'course_level' => $enrollment->course->level ?? 'intermediate',
                        'total_hours' => rand(15, 60),
                        'completion_rate' => rand(85, 100),
                    ],
                ]);
                $stats['regular']++;
            }
        }

        // 3. Generate passing certificates (D grade) - 15%
        $passingCount = (int) ($completedEnrollments->count() * 0.15);
        if ($passingCount > 0) {
            $this->command->info("Creating {$passingCount} passing certificates (Grade D)...");

            foreach ($completedEnrollments->skip($excellentCount + $regularCount)->take($passingCount) as $enrollment) {
                $this->createCertificate($enrollment, [
                    'final_score' => fake()->numberBetween(60, 69),
                    'grade' => 'D',
                    'metadata' => [
                        'duration_days' => rand(7, 60),
                        'course_level' => $enrollment->course->level ?? 'beginner',
                        'total_hours' => rand(10, 40),
                        'completion_rate' => rand(70, 85),
                    ],
                ]);
                $stats['regular']++;
            }
        }

        // 4. Generate some recent certificates (issued in last 7 days)
        $recentCount = min(10, Certificate::count());
        if ($recentCount > 0) {
            $this->command->info("Making {$recentCount} certificates recent...");

            Certificate::inRandomOrder()
                ->take($recentCount)
                ->update([
                    'issue_date' => now()->subDays(rand(1, 7)),
                    'completion_date' => now()->subDays(rand(1, 10)),
                ]);
            $stats['recent'] = $recentCount;
        }

        // 5. Generate some old certificates (issued 6-12 months ago)
        $oldCount = min(8, Certificate::count());
        if ($oldCount > 0) {
            $this->command->info("Making {$oldCount} certificates old...");

            Certificate::inRandomOrder()
                ->whereNotIn('id', Certificate::orderBy('issue_date', 'desc')->take($recentCount)->pluck('id'))
                ->take($oldCount)
                ->get()
                ->each(function ($certificate) {
                    $daysAgo = rand(180, 365);
                    $certificate->update([
                        'issue_date' => now()->subDays($daysAgo),
                        'completion_date' => now()->subDays($daysAgo + rand(1, 5)),
                    ]);
                });
            $stats['old'] = $oldCount;
        }

        // 6. Revoke a few certificates (about 5%)
        $revokeCount = max(1, (int) (Certificate::count() * 0.05));
        $this->command->info("Revoking {$revokeCount} certificates...");

        Certificate::inRandomOrder()
            ->take($revokeCount)
            ->get()
            ->each(function ($certificate) {
                $certificate->revoke(fake()->randomElement([
                    'Found violation of course policies',
                    'Student requested withdrawal',
                    'Duplicate certificate issued',
                    'Invalid assessment results',
                    'Course completion requirements not met',
                ]));
            });
        $stats['revoked'] = $revokeCount;

        // Calculate total
        $stats['total'] = Certificate::count();

        // Display statistics
        $this->command->newLine();
        $this->command->info('✅ Certificate seeding completed!');
        $this->command->newLine();
        $this->command->table(
            ['Metric', 'Count'],
            [
                ['Total Certificates', $stats['total']],
                ['Excellence (A)', $stats['excellence']],
                ['Regular (B-D)', $stats['regular']],
                ['Recent (< 7 days)', $stats['recent']],
                ['Old (> 6 months)', $stats['old']],
                ['Revoked', $stats['revoked']],
            ]
        );

        // Sample certificates info
        $this->command->newLine();
        $this->command->info('📋 Sample Certificates:');

        Certificate::with(['user', 'course'])
            ->latest()
            ->take(5)
            ->get()
            ->each(function ($cert) {
                $status = $cert->is_valid ? '✅' : '❌';
                $this->command->line(
                    "{$status} {$cert->certificate_number} - {$cert->student_name} - {$cert->course_title} - Grade: {$cert->grade}"
                );
            });
    }

    /**
     * Create a certificate with custom attributes
     */
    private function createCertificate(Enrollment $enrollment, array $customAttributes = []): Certificate
    {
        $score = $customAttributes['final_score'] ?? fake()->numberBetween(60, 100);
        $grade = $customAttributes['grade'] ?? match (true) {
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            $score >= 60 => 'D',
            default => 'F'
        };

        $completionDate = $enrollment->completed_at ?? now()->subDays(rand(1, 90));
        $issueDate = $completionDate->copy()->addDays(rand(0, 3));

        $defaultMetadata = [
            'duration_days' => $enrollment->enrolled_at
                ? $enrollment->enrolled_at->diffInDays($completionDate)
                : rand(7, 90),
            'course_level' => $enrollment->course->level ?? 'intermediate',
            'course_category' => $enrollment->course->category ?? 'General',
            'total_hours' => rand(10, 100),
            'instructor_name' => $enrollment->course->instructor->name ?? 'Unknown',
        ];

        $metadata = array_merge($defaultMetadata, $customAttributes['metadata'] ?? []);

        return Certificate::create([
            'enrollment_id' => $enrollment->id,
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->course_id,
            'certificate_number' => Certificate::generateCertificateNumber(),
            'student_name' => $enrollment->user->name,
            'course_title' => $enrollment->course->title,
            'course_description' => $enrollment->course->description,
            'issue_date' => $issueDate,
            'completion_date' => $completionDate,
            'final_score' => $score,
            'grade' => $grade,
            'instructor_name' => $enrollment->course->instructor->name ?? 'Academic Director',
            'signature' => null,
            'metadata' => $metadata,
            'is_valid' => true,
            'revoked_at' => null,
            'revoke_reason' => null,
        ]);
    }

    /**
     * Ensure we have completed enrollments
     */
    private function ensureCompletedEnrollments(): void
    {
        $completedCount = Enrollment::completed()->count();

        if ($completedCount < 20) {
            $this->command->info('Creating additional completed enrollments...');
            $this->createCompletedEnrollments(20 - $completedCount);
        }
    }

    /**
     * Create completed enrollments
     */
    private function createCompletedEnrollments(int $count = 20): void
    {
        $students = User::where('role', 'siswa')->get();
        $courses = Course::all();

        if ($students->isEmpty() || $courses->isEmpty()) {
            $this->command->warn('⚠️  Need students and courses to create enrollments!');
            return;
        }

        for ($i = 0; $i < $count; $i++) {
            $student = $students->random();
            $course = $courses->random();

            // Check if enrollment already exists
            $exists = Enrollment::where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->exists();

            if (!$exists) {
                $enrolledAt = now()->subDays(rand(30, 180));
                $completedAt = $enrolledAt->copy()->addDays(rand(7, 60));

                Enrollment::create([
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'enrolled_at' => $enrolledAt,
                    'progress' => 100,
                    'status' => 'completed',
                    'completed_at' => $completedAt,
                ]);
            }
        }
    }
}
