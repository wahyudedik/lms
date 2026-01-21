<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $course_id
 * @property string|null $title
 * @property string|null $context_type
 * @property int|null $context_id
 * @property int $message_count
 * @property int $tokens_used
 * @property \Illuminate\Support\Carbon|null $last_message_at
 * @property bool $is_archived
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course|null $course
 * @property-read \App\Models\AiMessage|null $latestMessage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AiMessage> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation byContext(string $type, ?int $id = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereContextId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereContextType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereLastMessageAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereMessageCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereTokensUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereUserId($value)
 */
	class AiConversation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $conversation_id
 * @property string $role
 * @property string $content
 * @property int $tokens
 * @property string|null $model
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AiConversation $conversation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage assistant()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage system()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage user()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereUpdatedAt($value)
 */
	class AiMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $attempt_id
 * @property int $question_id
 * @property array<array-key, mixed>|null $answer
 * @property bool|null $is_correct
 * @property numeric|null $points_earned
 * @property string|null $feedback
 * @property array<array-key, mixed>|null $shuffled_options
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $saved_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ExamAttempt $attempt
 * @property-read string $formatted_answer
 * @property-read string $formatted_correct_answer
 * @property-read \App\Models\Question $question
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereAttemptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereFeedback($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereIsCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer wherePointsEarned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereSavedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereShuffledOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereUpdatedAt($value)
 */
	class Answer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $resource_type
 * @property int|null $resource_id
 * @property string $action
 * @property string $ability
 * @property string $result
 * @property string|null $reason
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $route
 * @property string|null $method
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $resource
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog allowed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog denied()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog forResource(string $resourceType)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog forUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereAbility($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereResourceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereUserId($value)
 */
	class AuthorizationLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $enrollment_id
 * @property int $user_id
 * @property int $course_id
 * @property string $certificate_number
 * @property string $student_name
 * @property string $course_title
 * @property string|null $course_description
 * @property \Illuminate\Support\Carbon $issue_date
 * @property \Illuminate\Support\Carbon $completion_date
 * @property int|null $final_score
 * @property string|null $grade
 * @property string|null $instructor_name
 * @property string|null $signature
 * @property array<array-key, mixed>|null $metadata
 * @property bool $is_valid
 * @property \Illuminate\Support\Carbon|null $revoked_at
 * @property string|null $revoke_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\Enrollment $enrollment
 * @property-read string $download_url
 * @property-read string $grade_color
 * @property-read string $status_color
 * @property-read string $status_display
 * @property-read string $verification_url
 * @property-read string $view_url
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate byCertificateNumber(string $number)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate byCourse(int $courseId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate byUser(int $userId)
 * @method static \Database\Factories\CertificateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate recentlyIssued(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate revoked()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate valid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereCertificateNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereCompletionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereCourseDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereCourseTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereEnrollmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereFinalScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereInstructorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereIsValid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereIssueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereRevokeReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereRevokedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereStudentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereUserId($value)
 */
	class Certificate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Exam|null $exam
 * @property-read \App\Models\ExamAttempt|null $examAttempt
 * @property-read string $status_badge
 * @property-read \App\Models\User|null $resolver
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident blocked()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident recent(int $days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident resolved()
 */
	class CheatingIncident extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $code
 * @property string|null $description
 * @property int $instructor_id
 * @property string $status
 * @property string|null $cover_image
 * @property int|null $max_students
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Enrollment> $enrollments
 * @property-read int|null $enrollments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Exam> $exams
 * @property-read int|null $exams_count
 * @property-read string $status_color
 * @property-read string $status_display
 * @property-read \App\Models\User $instructor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Material> $materials
 * @property-read int|null $materials_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $students
 * @property-read int|null $students_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course byInstructor($instructorId)
 * @method static \Database\Factories\CourseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereInstructorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereMaxStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereUpdatedAt($value)
 */
	class Course extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $course_id
 * @property string $status
 * @property int $progress
 * @property \Illuminate\Support\Carbon $enrolled_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Certificate|null $certificate
 * @property-read \App\Models\Course $course
 * @property-read string $progress_color
 * @property-read string $status_color
 * @property-read string $status_display
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment byCourse($courseId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment byStudent($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment completed()
 * @method static \Database\Factories\EnrollmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment whereEnrolledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment whereProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment whereUserId($value)
 */
	class Enrollment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $course_id
 * @property int $created_by
 * @property string $title
 * @property string|null $description
 * @property string|null $instructions
 * @property int $duration_minutes
 * @property \Illuminate\Support\Carbon|null $start_time
 * @property \Illuminate\Support\Carbon|null $end_time
 * @property int $max_attempts
 * @property bool $shuffle_questions
 * @property bool $shuffle_options
 * @property bool $show_results_immediately
 * @property bool $show_correct_answers
 * @property bool $allow_token_access
 * @property string|null $access_token
 * @property bool $require_guest_name
 * @property bool $require_guest_email
 * @property int|null $max_token_uses Null = unlimited
 * @property int $current_token_uses
 * @property numeric $pass_score
 * @property bool $require_fullscreen
 * @property bool $detect_tab_switch
 * @property int $max_tab_switches
 * @property bool $is_published
 * @property bool $offline_enabled
 * @property int $offline_cache_duration Hours
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamAttempt> $attempts
 * @property-read int|null $attempts_count
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\User $creator
 * @property-read string $status_badge
 * @property-read float $total_points
 * @property-read int $total_questions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $questions
 * @property-read int|null $questions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam active()
 * @method static \Database\Factories\ExamFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereAllowTokenAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereCurrentTokenUses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereDetectTabSwitch($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereDurationMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereMaxAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereMaxTabSwitches($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereMaxTokenUses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereOfflineCacheDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereOfflineEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam wherePassScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereRequireFullscreen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereRequireGuestEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereRequireGuestName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereShowCorrectAnswers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereShowResultsImmediately($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereShuffleOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereShuffleQuestions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereUpdatedAt($value)
 */
	class Exam extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $exam_id
 * @property int|null $user_id
 * @property int $is_offline
 * @property bool $is_guest
 * @property string|null $guest_name
 * @property string|null $guest_email
 * @property string|null $guest_token
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property int|null $time_spent
 * @property numeric|null $score
 * @property int|null $correct_answers
 * @property int|null $total_questions
 * @property numeric|null $total_points_earned
 * @property numeric|null $total_points_possible
 * @property bool|null $passed
 * @property string $status
 * @property int $tab_switches
 * @property int $fullscreen_exits
 * @property array<array-key, mixed>|null $violations
 * @property array<array-key, mixed>|null $shuffled_question_ids
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Answer> $answers
 * @property-read int|null $answers_count
 * @property-read \App\Models\Exam $exam
 * @property-read string $formatted_time_spent
 * @property-read string $status_badge
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereCorrectAnswers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereFullscreenExits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereGuestEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereGuestName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereGuestToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereIsGuest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereIsOffline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt wherePassed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereShuffledQuestionIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereTabSwitches($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereTimeSpent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereTotalPointsEarned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereTotalPointsPossible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereTotalQuestions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExamAttempt whereViolations($value)
 */
	class ExamAttempt extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $icon
 * @property string $color
 * @property int $order
 * @property bool $is_active
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $creator
 * @property-read string $color_badge
 * @property-read int $replies_count
 * @property-read int|null $threads_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ForumThread> $threads
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumCategory active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumCategory ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumCategory whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumCategory whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumCategory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumCategory whereUpdatedAt($value)
 */
	class ForumCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $likeable_type
 * @property int $likeable_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $likeable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumLike newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumLike query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumLike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumLike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumLike whereLikeableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumLike whereLikeableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumLike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumLike whereUserId($value)
 */
	class ForumLike extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $thread_id
 * @property int|null $parent_id
 * @property int $user_id
 * @property string $content
 * @property-read int|null $likes_count
 * @property bool $is_solution
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ForumReply> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ForumLike> $likes
 * @property-read ForumReply|null $parent
 * @property-read \App\Models\ForumThread $thread
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply parents()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply whereIsSolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply whereLikesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply whereThreadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply withUser()
 */
	class ForumReply extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $category_id
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property bool $is_pinned
 * @property bool $is_locked
 * @property int $views_count
 * @property-read int|null $replies_count
 * @property-read int|null $likes_count
 * @property \Illuminate\Support\Carbon|null $last_activity_at
 * @property int|null $last_reply_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ForumCategory $category
 * @property-read string $excerpt
 * @property-read string $status_badge
 * @property-read \App\Models\User|null $lastReplyUser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ForumLike> $likes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ForumReply> $replies
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread latestActivity()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread notLocked()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread pinned()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread popular()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread search($search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereIsLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereIsPinned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereLastActivityAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereLastReplyUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereLikesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereRepliesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereViewsCount($value)
 */
	class ForumThread extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $course_id
 * @property int $created_by
 * @property string $title
 * @property string|null $description
 * @property string $type
 * @property string|null $file_path
 * @property string|null $file_name
 * @property int|null $file_size
 * @property string|null $url
 * @property int $order
 * @property bool $is_published
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MaterialComment> $allComments
 * @property-read int|null $all_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MaterialComment> $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\User $creator
 * @property-read string $type_display
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material byCourse($courseId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereUrl($value)
 */
	class Material extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $material_id
 * @property int $user_id
 * @property int|null $parent_id
 * @property string $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Material $material
 * @property-read MaterialComment|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, MaterialComment> $replies
 * @property-read int|null $replies_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment whereMaterialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment whereUserId($value)
 */
	class MaterialComment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $exam_id
 * @property int|null $question_bank_id
 * @property bool $is_from_bank
 * @property string $type
 * @property string $question_text
 * @property string|null $question_image
 * @property array<array-key, mixed>|null $options
 * @property array<array-key, mixed>|null $pairs
 * @property array<array-key, mixed>|null $correct_answer
 * @property numeric $points
 * @property int $order
 * @property string|null $explanation
 * @property string $essay_grading_mode Mode penilaian essay: manual, keyword matching, atau similarity
 * @property array<array-key, mixed>|null $essay_keywords Array kata kunci untuk keyword matching
 * @property array<array-key, mixed>|null $essay_keyword_points Bobot poin per kata kunci
 * @property string|null $essay_model_answer Jawaban model/referensi untuk similarity matching
 * @property int $essay_min_similarity Minimal % similarity untuk lulus (0-100)
 * @property numeric|null $essay_similarity_penalty Penalty poin jika similarity di bawah minimum
 * @property bool $essay_case_sensitive Apakah penilaian case-sensitive
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Answer> $answers
 * @property-read int|null $answers_count
 * @property-read \App\Models\Exam $exam
 * @property-read string $essay_grading_mode_display
 * @property-read string $type_display
 * @property-read string $type_icon
 * @property-read \App\Models\QuestionBank|null $questionBank
 * @method static \Database\Factories\QuestionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereCorrectAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereEssayCaseSensitive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereEssayGradingMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereEssayKeywordPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereEssayKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereEssayMinSimilarity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereEssayModelAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereEssaySimilarityPenalty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereExplanation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereIsFromBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question wherePairs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereQuestionBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereQuestionImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereQuestionText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereUpdatedAt($value)
 */
	class Question extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $category_id
 * @property int $created_by
 * @property string $type
 * @property string $question_text
 * @property string|null $question_image
 * @property string $difficulty
 * @property array<array-key, mixed>|null $tags
 * @property numeric $default_points
 * @property array<array-key, mixed>|null $options
 * @property string|null $correct_answer
 * @property array<array-key, mixed>|null $correct_answer_multiple
 * @property array<array-key, mixed>|null $pairs
 * @property string $essay_grading_mode
 * @property array<array-key, mixed>|null $essay_keywords
 * @property string|null $essay_model_answer
 * @property numeric|null $essay_min_similarity
 * @property numeric|null $essay_similarity_penalty
 * @property bool $essay_case_sensitive
 * @property string|null $explanation
 * @property string|null $teacher_notes
 * @property int $times_used
 * @property numeric|null $average_score
 * @property int $times_correct
 * @property int $times_incorrect
 * @property bool $is_active
 * @property bool $is_verified
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\QuestionBankCategory|null $category
 * @property-read \App\Models\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $examQuestions
 * @property-read int|null $exam_questions_count
 * @property-read string $difficulty_badge
 * @property-read float $success_rate
 * @property-read string $type_badge
 * @property-read string $verification_badge
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank inCategory(int $categoryId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank ofDifficulty(string $difficulty)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank search(string $search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank verified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereAverageScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereCorrectAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereCorrectAnswerMultiple($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereDefaultPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereEssayCaseSensitive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereEssayGradingMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereEssayKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereEssayMinSimilarity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereEssayModelAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereEssaySimilarityPenalty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereExplanation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank wherePairs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereQuestionImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereQuestionText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereTeacherNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereTimesCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereTimesIncorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereTimesUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereUpdatedAt($value)
 */
	class QuestionBank extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $color
 * @property int $order
 * @property bool $is_active
 * @property int|null $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuestionBank> $activeQuestions
 * @property-read int|null $active_questions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, QuestionBankCategory> $children
 * @property-read int|null $children_count
 * @property-read int $active_question_count
 * @property-read string $color_badge
 * @property-read string $full_path
 * @property-read int $question_count
 * @property-read QuestionBankCategory|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuestionBank> $questions
 * @property-read int|null $questions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory roots()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereUpdatedAt($value)
 */
	class QuestionBankCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $filename
 * @property string|null $file_path
 * @property int|null $file_size
 * @property string $status
 * @property int $total_rows
 * @property int $imported_count
 * @property int $skipped_count
 * @property int $error_count
 * @property array<array-key, mixed>|null $errors
 * @property array<array-key, mixed>|null $summary
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property int|null $processing_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $formatted_file_size
 * @property-read mixed $status_badge
 * @property-read mixed $success_rate
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereErrorCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereErrors($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereImportedCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereProcessingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereSkippedCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereTotalRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereUserId($value)
 */
	class QuestionBankImportHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $logo
 * @property string|null $favicon
 * @property string|null $domain
 * @property bool $is_active
 * @property bool $show_landing_page
 * @property string|null $hero_title
 * @property string|null $hero_subtitle
 * @property string|null $hero_description
 * @property string|null $hero_image
 * @property string $hero_cta_text
 * @property string|null $hero_cta_link
 * @property string|null $about_title
 * @property string|null $about_content
 * @property string|null $about_image
 * @property string|null $features
 * @property string|null $statistics
 * @property string|null $contact_address
 * @property string|null $contact_phone
 * @property string|null $contact_email
 * @property string|null $contact_whatsapp
 * @property string|null $social_facebook
 * @property string|null $social_instagram
 * @property string|null $social_twitter
 * @property string|null $social_youtube
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $about_image_url
 * @property-read mixed $favicon_url
 * @property-read mixed $hero_image_url
 * @property-read mixed $logo_url
 * @property-read int|null $users_count
 * @property-read \App\Models\SchoolTheme|null $theme
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereAboutContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereAboutImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereAboutTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereContactAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereContactWhatsapp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereFavicon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereHeroCtaLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereHeroCtaText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereHeroDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereHeroImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereHeroSubtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereHeroTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereShowLandingPage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSocialFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSocialInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSocialTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSocialYoutube($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereStatistics($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereUpdatedAt($value)
 */
	class School extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $school_id
 * @property string $primary_color
 * @property string $secondary_color
 * @property string $accent_color
 * @property string $success_color
 * @property string $warning_color
 * @property string $danger_color
 * @property string $info_color
 * @property string $dark_color
 * @property string $text_primary
 * @property string $text_secondary
 * @property string $text_muted
 * @property string $background_color
 * @property string $card_background
 * @property string $navbar_background
 * @property string $sidebar_background
 * @property string $font_family
 * @property string|null $heading_font
 * @property int $font_size
 * @property string|null $custom_css
 * @property string|null $login_background
 * @property string|null $dashboard_hero
 * @property string $border_radius
 * @property string $box_shadow
 * @property bool $dark_mode
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $dashboard_hero_url
 * @property-read mixed $login_background_url
 * @property-read \App\Models\School $school
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereAccentColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereBorderRadius($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereBoxShadow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereCardBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereCustomCss($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereDangerColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereDarkColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereDarkMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereDashboardHero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereFontFamily($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereFontSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereHeadingFont($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereInfoColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereLoginBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereNavbarBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereSecondaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereSidebarBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereSuccessColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereTextMuted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereTextPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereTextSecondary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereWarningColor($value)
 */
	class SchoolTheme extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string $type
 * @property string $group
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereValue($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $school_id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property string|null $phone
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property string|null $gender
 * @property string|null $address
 * @property string|null $profile_photo
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CheatingIncident> $cheatingIncidents
 * @property-read int|null $cheating_incidents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Exam> $createdExams
 * @property-read int|null $created_exams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $enrolledCourses
 * @property-read int|null $enrolled_courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Enrollment> $enrollments
 * @property-read int|null $enrollments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamAttempt> $examAttempts
 * @property-read int|null $exam_attempts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ForumLike> $forumLikes
 * @property-read int|null $forum_likes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ForumReply> $forumReplies
 * @property-read int|null $forum_replies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ForumThread> $forumThreads
 * @property-read int|null $forum_threads_count
 * @property-read string|null $dashboard_route
 * @property-read int $forum_posts_count
 * @property-read bool $is_login_blocked
 * @property-read string $profile_photo_path
 * @property-read string $profile_photo_url
 * @property-read string $role_display
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\School|null $school
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $teachingCourses
 * @property-read int|null $teaching_courses_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $activity_type
 * @property string|null $activity_name
 * @property string|null $description
 * @property array<array-key, mixed>|null $metadata
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property int|null $duration_seconds
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivityLog whereActivityName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivityLog whereActivityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivityLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivityLog whereDurationSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivityLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivityLog whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivityLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivityLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivityLog whereUserId($value)
 */
	class UserActivityLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $session_id
 * @property \Illuminate\Support\Carbon $login_at
 * @property \Illuminate\Support\Carbon|null $logout_at
 * @property int|null $duration_seconds
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $device_type
 * @property string|null $browser
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereDurationSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereLogoutAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereUserId($value)
 */
	class UserSession extends \Eloquent {}
}

