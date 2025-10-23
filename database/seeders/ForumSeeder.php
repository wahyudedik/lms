<?php

namespace Database\Seeders;

use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\ForumReply;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $guru = User::where('role', 'guru')->first();
        $siswa = User::where('role', 'siswa')->first();

        // Create categories
        $categories = [
            [
                'name' => 'General Discussion',
                'slug' => 'general',
                'description' => 'Talk about anything related to learning',
                'icon' => 'fas fa-comments',
                'color' => '#3B82F6',
                'order' => 1,
            ],
            [
                'name' => 'Course Help',
                'slug' => 'course-help',
                'description' => 'Get help with your courses and lessons',
                'icon' => 'fas fa-question-circle',
                'color' => '#10B981',
                'order' => 2,
            ],
            [
                'name' => 'Exam Discussion',
                'slug' => 'exam-discussion',
                'description' => 'Discuss exam strategies and questions',
                'icon' => 'fas fa-file-alt',
                'color' => '#F59E0B',
                'order' => 3,
            ],
            [
                'name' => 'Announcements',
                'slug' => 'announcements',
                'description' => 'Official announcements from teachers',
                'icon' => 'fas fa-bullhorn',
                'color' => '#EF4444',
                'order' => 0,
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = ForumCategory::create([
                ...$categoryData,
                'created_by' => $admin?->id ?? 1,
            ]);

            // Create threads for each category
            if ($category->slug === 'general') {
                $thread = ForumThread::create([
                    'category_id' => $category->id,
                    'user_id' => $admin?->id ?? 1,
                    'title' => 'Welcome to the Forum!',
                    'slug' => 'welcome-to-the-forum',
                    'content' => "Hi everyone! 👋\n\nWelcome to our learning forum. This is a place where we can discuss, share, and learn together.\n\nFeel free to ask questions, share your knowledge, and connect with fellow learners.\n\nLet's build a great community!",
                    'is_pinned' => true,
                    'last_activity_at' => now(),
                ]);

                ForumReply::create([
                    'thread_id' => $thread->id,
                    'user_id' => $guru?->id ?? 2,
                    'content' => 'Great to have everyone here! Looking forward to learning together.',
                ]);

                ForumReply::create([
                    'thread_id' => $thread->id,
                    'user_id' => $siswa?->id ?? 3,
                    'content' => 'Thank you! Excited to be part of this community! 😊',
                ]);
            }

            if ($category->slug === 'course-help' && $siswa) {
                $thread = ForumThread::create([
                    'category_id' => $category->id,
                    'user_id' => $siswa->id,
                    'title' => 'Need help understanding the material',
                    'slug' => 'need-help-understanding-material',
                    'content' => "Hi everyone,\n\nI'm having trouble understanding some of the concepts from the latest lesson. Can someone explain it in simpler terms?\n\nThanks in advance!",
                    'last_activity_at' => now()->subHours(2),
                ]);

                $reply = ForumReply::create([
                    'thread_id' => $thread->id,
                    'user_id' => $guru?->id ?? 2,
                    'content' => "Sure! Let me break it down for you:\n\n1. First concept...\n2. Second concept...\n\nDoes this help?",
                ]);

                ForumReply::create([
                    'thread_id' => $thread->id,
                    'user_id' => $siswa->id,
                    'content' => 'Yes! That makes much more sense now. Thank you so much!',
                    'parent_id' => $reply->id,
                ]);
            }

            if ($category->slug === 'exam-discussion' && $siswa) {
                ForumThread::create([
                    'category_id' => $category->id,
                    'user_id' => $siswa->id,
                    'title' => 'Tips for preparing for the upcoming exam?',
                    'slug' => 'tips-for-preparing-exam',
                    'content' => "The exam is coming up next week. What are your study strategies?\n\nI'd love to hear what works for everyone!",
                    'last_activity_at' => now()->subDay(),
                ]);
            }

            if ($category->slug === 'announcements') {
                ForumThread::create([
                    'category_id' => $category->id,
                    'user_id' => $admin?->id ?? 1,
                    'title' => 'Forum Rules and Guidelines',
                    'slug' => 'forum-rules-guidelines',
                    'content' => "📋 Forum Rules:\n\n1. Be respectful to everyone\n2. No spam or self-promotion\n3. Stay on topic\n4. Use appropriate language\n5. Help and support each other\n\nViolations may result in warnings or account suspension.",
                    'is_pinned' => true,
                    'last_activity_at' => now()->subWeek(),
                ]);
            }
        }

        $this->command->info('Forum seeded successfully with ' . ForumCategory::count() . ' categories, ' . ForumThread::count() . ' threads, and ' . ForumReply::count() . ' replies!');
    }
}
