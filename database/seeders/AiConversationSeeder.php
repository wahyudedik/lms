<?php

namespace Database\Seeders;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Seeder;

class AiConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🤖 Seeding AI conversations...');

        $users = User::where('role', 'siswa')->limit(10)->get();
        $courses = Course::limit(5)->get();

        if ($users->isEmpty()) {
            $this->command->warn('No student users found. Skipping AI conversation seeding.');
            return;
        }

        $sampleConversations = [
            [
                'context_type' => 'course',
                'exchanges' => [
                    [
                        'user' => 'Can you explain what object-oriented programming is?',
                        'assistant' => "Object-Oriented Programming (OOP) is a programming paradigm that organizes code into 'objects' - self-contained units that combine data (attributes) and functions (methods) that operate on that data.\n\nKey concepts of OOP include:\n\n1. **Classes**: Blueprints for creating objects\n2. **Objects**: Instances of classes\n3. **Encapsulation**: Bundling data with methods that operate on that data\n4. **Inheritance**: Creating new classes based on existing ones\n5. **Polymorphism**: Objects of different types responding to the same method call\n\nOOP helps make code more modular, reusable, and easier to maintain. Would you like me to explain any of these concepts in more detail?"
                    ],
                    [
                        'user' => 'Yes, can you give me an example of inheritance?',
                        'assistant' => "Great question! Here's a simple example of inheritance:\n\nImagine you have a general `Vehicle` class:\n```\nclass Vehicle:\n    def __init__(self, brand, model):\n        self.brand = brand\n        self.model = model\n    \n    def start_engine(self):\n        print('Engine started!')\n```\n\nNow you can create specific vehicle types that **inherit** from Vehicle:\n```\nclass Car(Vehicle):\n    def __init__(self, brand, model, doors):\n        super().__init__(brand, model)  # Inherit brand & model\n        self.doors = doors\n    \n    def honk(self):\n        print('Beep beep!')\n```\n\nThe `Car` class inherits all properties and methods from `Vehicle`, but adds its own specific features (like `doors` and `honk()`).\n\nThis is inheritance - the Car 'inherits' common vehicle properties while adding its own specialized features!"
                    ]
                ]
            ],
            [
                'context_type' => 'general',
                'exchanges' => [
                    [
                        'user' => 'What are some effective study strategies for online learning?',
                        'assistant' => "Great question! Here are some proven strategies for online learning:\n\n**1. Create a Dedicated Study Space**\n- Find a quiet, distraction-free area\n- Keep it organized and comfortable\n- Ensure good lighting and ergonomics\n\n**2. Set a Regular Schedule**\n- Block out specific study times\n- Take regular breaks (Pomodoro technique: 25 min study, 5 min break)\n- Stick to consistent daily routines\n\n**3. Active Learning**\n- Take notes by hand when possible\n- Summarize lessons in your own words\n- Teach concepts to someone else\n- Practice with exercises and quizzes\n\n**4. Stay Engaged**\n- Participate in forums and discussions\n- Ask questions when confused\n- Connect with classmates\n\n**5. Minimize Distractions**\n- Turn off phone notifications\n- Use website blockers during study time\n- Close unnecessary browser tabs\n\nWhich of these would you like to explore further?"
                    ]
                ]
            ],
            [
                'context_type' => 'course',
                'exchanges' => [
                    [
                        'user' => 'I\'m struggling with understanding SQL joins. Can you help?',
                        'assistant' => "Absolutely! SQL joins can be tricky at first. Let me break them down:\n\n**INNER JOIN** - Only returns rows where there's a match in BOTH tables\n```sql\nSELECT students.name, enrollments.course\nFROM students\nINNER JOIN enrollments ON students.id = enrollments.student_id;\n```\nResult: Only students who are enrolled in courses\n\n**LEFT JOIN** - Returns ALL rows from left table, matching rows from right\n```sql\nSELECT students.name, enrollments.course\nFROM students\nLEFT JOIN enrollments ON students.id = enrollments.student_id;\n```\nResult: All students, even if not enrolled (course would be NULL)\n\n**RIGHT JOIN** - Returns ALL rows from right table, matching rows from left\n(Less common, opposite of LEFT JOIN)\n\n**FULL OUTER JOIN** - Returns ALL rows from both tables\n\n**Think of it like this:**\n- INNER = Intersection (both have)\n- LEFT = Everything from left table\n- RIGHT = Everything from right table\n- FULL = Union (everything from both)\n\nWould you like to practice with an example?"
                    ],
                    [
                        'user' => 'Yes, can you give me a practice question?',
                        'assistant' => "Great! Here's a practice scenario:\n\n**Database Setup:**\nYou have two tables:\n- `courses` (id, title, instructor_id)\n- `instructors` (id, name, department)\n\n**Question:**\nWrite a query to show all courses along with their instructor names. If a course has no assigned instructor, it should still appear in the results with NULL for the instructor name.\n\n**Hint:** Think about which JOIN type ensures ALL courses appear, even those without instructors.\n\nTry writing the query, and let me know what you come up with! I'm here to guide you, not give the direct answer, so you can learn better. 😊"
                    ]
                ]
            ],
            [
                'context_type' => 'exam',
                'exchanges' => [
                    [
                        'user' => 'I have an exam coming up on data structures. What should I focus on?',
                        'assistant' => "Good thinking to prepare ahead! For data structures exams, here are key topics to master:\n\n**Core Data Structures:**\n1. **Arrays & Strings** - Basics, manipulation, common algorithms\n2. **Linked Lists** - Single, double, circular variants\n3. **Stacks & Queues** - LIFO/FIFO operations, implementations\n4. **Trees** - Binary trees, BST, tree traversals (in-order, pre-order, post-order)\n5. **Hash Tables** - Hashing, collision resolution\n6. **Graphs** - Representations, BFS, DFS\n\n**Key Concepts:**\n- Time & Space Complexity (Big O notation)\n- When to use each data structure\n- Trade-offs between structures\n\n**Study Tips:**\n- Draw diagrams of each structure\n- Practice implementing from scratch\n- Solve problems on each topic\n- Understand WHY we choose one structure over another\n\n**Review your course materials** - especially examples covered in class. Practice problems are key!\n\nWhich topic would you like to review first?"
                    ]
                ]
            ],
            [
                'context_type' => 'general',
                'exchanges' => [
                    [
                        'user' => 'How can I stay motivated when learning gets difficult?',
                        'assistant' => "Great question - staying motivated through challenges is crucial! Here's my advice:\n\n**1. Remember Your 'Why'**\n- Why did you start this course?\n- What goals will this help you achieve?\n- Keep your end goal visible\n\n**2. Break It Down**\n- Large challenges feel overwhelming\n- Break into smaller, manageable tasks\n- Celebrate small wins!\n\n**3. Embrace the Struggle**\n- Difficulty means you're learning\n- Your brain is literally growing new connections\n- Mistakes are part of the process\n\n**4. Use Support Systems**\n- Study with classmates\n- Ask questions in forums\n- Reach out to instructors\n- Use AI assistants like me! 😊\n\n**5. Take Care of Yourself**\n- Get enough sleep\n- Exercise regularly\n- Take breaks\n- Reward yourself\n\n**6. Mix Up Your Methods**\n- Try videos, reading, practice problems\n- Find what works for your learning style\n- Sometimes a different explanation clicks\n\n**Remember:** The fact that you're here asking this question shows you care about your learning. That's already a huge step! What specific challenge are you facing right now?"
                    ]
                ]
            ]
        ];

        $createdCount = 0;

        foreach ($users as $user) {
            // Create 1-3 conversations per user
            $numConversations = rand(1, 3);

            for ($i = 0; $i < $numConversations; $i++) {
                $sample = $sampleConversations[array_rand($sampleConversations)];
                $course = $sample['context_type'] === 'course' && $courses->isNotEmpty()
                    ? $courses->random()
                    : null;

                // Create conversation
                $conversation = AiConversation::create([
                    'user_id' => $user->id,
                    'course_id' => $course?->id,
                    'context_type' => $sample['context_type'],
                    'context_id' => $course?->id,
                    'title' => null, // Will be auto-generated
                ]);

                // Create messages
                $totalTokens = 0;
                foreach ($sample['exchanges'] as $exchange) {
                    // User message
                    $userTokens = (int) ceil(strlen($exchange['user']) / 4);
                    AiMessage::create([
                        'conversation_id' => $conversation->id,
                        'role' => 'user',
                        'content' => $exchange['user'],
                        'tokens' => $userTokens,
                        'model' => 'gpt-3.5-turbo',
                    ]);
                    $totalTokens += $userTokens;

                    // Assistant message
                    $assistantTokens = (int) ceil(strlen($exchange['assistant']) / 4);
                    AiMessage::create([
                        'conversation_id' => $conversation->id,
                        'role' => 'assistant',
                        'content' => $exchange['assistant'],
                        'tokens' => $assistantTokens,
                        'model' => 'gpt-3.5-turbo',
                        'metadata' => [
                            'finish_reason' => 'stop',
                            'prompt_tokens' => $userTokens,
                            'total_tokens' => $userTokens + $assistantTokens,
                        ],
                    ]);
                    $totalTokens += $assistantTokens;
                }

                // Update conversation stats and generate title
                $conversation->updateStats();
                $conversation->generateTitle();

                $createdCount++;
            }
        }

        $this->command->info("✅ Created {$createdCount} AI conversations with sample messages!\n");

        // Show statistics
        $totalMessages = AiMessage::count();
        $totalTokens = AiConversation::sum('tokens_used');
        $avgMessages = AiConversation::avg('message_count');

        $this->command->table(
            ['Metric', 'Value'],
            [
                ['Total Conversations', $createdCount],
                ['Total Messages', $totalMessages],
                ['Total Tokens Used', number_format($totalTokens)],
                ['Avg Messages per Conversation', round($avgMessages, 1)],
            ]
        );
    }
}
