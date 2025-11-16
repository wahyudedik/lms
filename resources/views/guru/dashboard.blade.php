<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-chalkboard-teacher mr-2"></i>{{ __('Dashboard Guru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome Message -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <h3 class="text-2xl font-bold mb-2">{{ __('Selamat datang, :name! 👨‍🏫', ['name' => auth()->user()->name]) }}
                </h3>
                <p class="text-green-100">{{ __('Anda login sebagai :role', ['role' => auth()->user()->role_display]) }}
                </p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">{{ __('My Courses') }}</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_courses']) }}</p>
                            <p class="text-green-100 text-xs mt-1">
                                <i class="fas fa-book mr-1"></i>{{ __('Teaching') }}
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-book text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm">{{ __('Total Students') }}</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_students']) }}</p>
                            <p class="text-blue-100 text-xs mt-1">
                                <i class="fas fa-user-graduate mr-1"></i>{{ __('Enrolled') }}
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm">{{ __('Total Exams') }}</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_exams']) }}</p>
                            <p class="text-purple-100 text-xs mt-1">
                                <i class="fas fa-clipboard-list mr-1"></i>{{ __('Created') }}
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-clipboard-list text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm">{{ __('Pending Reviews') }}</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['pending_essays']) }}</p>
                            <p class="text-orange-100 text-xs mt-1">
                                <i class="fas fa-pen mr-1"></i>{{ __('Essays') }}
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-tasks text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4"><i
                        class="fas fa-bolt text-yellow-500 mr-2"></i>{{ __('Quick Actions') }}</h4>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <a href="{{ route('guru.courses.create') }}"
                        class="flex items-center p-4 bg-green-50 rounded-lg border-2 border-green-200 hover:border-green-400 hover:shadow-md transition-all">
                        <div class="bg-green-500 rounded-full p-3 text-white mr-3">
                            <i class="fas fa-book"></i>
                        </div>
                        <span class="text-gray-700 font-medium">{{ __('Create Course') }}</span>
                    </a>
                    <a href="{{ route('guru.exams.index') }}"
                        class="flex items-center p-4 bg-purple-50 rounded-lg border-2 border-purple-200 hover:border-purple-400 hover:shadow-md transition-all">
                        <div class="bg-purple-500 rounded-full p-3 text-white mr-3">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <span class="text-gray-700 font-medium">{{ __('My Exams') }}</span>
                    </a>
                    <a href="{{ route('guru.analytics.index') }}"
                        class="flex items-center p-4 bg-blue-50 rounded-lg border-2 border-blue-200 hover:border-blue-400 hover:shadow-md transition-all">
                        <div class="bg-blue-500 rounded-full p-3 text-white mr-3">
                            <i class="fas fa-chart-area"></i>
                        </div>
                        <span class="text-gray-700 font-medium">{{ __('Analytics') }}</span>
                    </a>
                    <a href="{{ route('guru.reports.index') }}"
                        class="flex items-center p-4 bg-orange-50 rounded-lg border-2 border-orange-200 hover:border-orange-400 hover:shadow-md transition-all">
                        <div class="bg-orange-500 rounded-full p-3 text-white mr-3">
                            <i class="fas fa-file-export"></i>
                        </div>
                        <span class="text-gray-700 font-medium">{{ __('Reports') }}</span>
                    </a>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- My Courses -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900"><i
                                class="fas fa-book-open text-green-500 mr-2"></i>{{ __('My Courses') }}</h4>
                        <a href="{{ route('guru.courses.index') }}"
                            class="text-sm text-blue-600 hover:text-blue-800">{{ __('View all →') }}</a>
                    </div>
                    @forelse($recentCourses as $course)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg mb-2 hover:bg-gray-100 transition">
                            <div class="bg-green-100 rounded-full p-2 text-green-600 mr-3">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $course->title }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ trans_choice(__(':count student enrolled|:count students enrolled'), $course->enrollments->count(), ['count' => $course->enrollments->count()]) }}
                                </p>
                            </div>
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($course->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-book text-gray-400 text-4xl mb-3"></i>
                            <p class="text-gray-500 mb-3">{{ __('No courses yet') }}</p>
                            <a href="{{ route('guru.courses.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-plus mr-2"></i>{{ __('Create Course') }}
                            </a>
                        </div>
                    @endforelse
                </div>

                <!-- Upcoming Exams -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900"><i
                                class="fas fa-calendar-alt text-purple-500 mr-2"></i>{{ __('Upcoming Exams') }}</h4>
                        <a href="{{ route('guru.exams.index') }}"
                            class="text-sm text-blue-600 hover:text-blue-800">{{ __('View all →') }}</a>
                    </div>
                    @forelse($upcomingExams as $exam)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg mb-2 hover:bg-gray-100 transition">
                            <div class="bg-purple-100 rounded-full p-2 text-purple-600 mr-3">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $exam->title }}</p>
                                <p class="text-xs text-gray-500">{{ $exam->course->title }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">
                                    {{ $exam->start_time ? $exam->start_time->translatedFormat('d M') : __('No date') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">{{ __('No upcoming exams') }}</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900"><i
                            class="fas fa-history text-blue-500 mr-2"></i>{{ __('Recent Exam Submissions') }}</h4>
                </div>
                @forelse($recentAttempts as $attempt)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg mb-2 hover:bg-gray-100 transition">
                        <img class="h-10 w-10 rounded-full object-cover mr-3"
                            src="{{ $attempt->user->profile_photo_url }}" alt="{{ $attempt->user->name }}">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $attempt->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $attempt->exam->title }}</p>
                        </div>
                        <div class="text-right mr-4">
                            <p class="text-sm font-bold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($attempt->score, 1) }}%</p>
                            <p class="text-xs text-gray-500">{{ $attempt->submitted_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('guru.exams.show', $attempt->exam) }}"
                            class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-clipboard text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-500">{{ __('No recent submissions') }}</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
