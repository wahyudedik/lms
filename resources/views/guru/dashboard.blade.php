<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-chalkboard-teacher mr-2"></i>{{ __('Dashboard Guru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome Message -->
            <div class="bg-green-600 rounded-lg shadow-md p-6 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chalkboard-teacher text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold mb-1">
                            {{ __('Selamat datang, :name!', ['name' => auth()->user()->name]) }}</h3>
                        <p class="text-green-100">
                            {{ __('Anda login sebagai :role', ['role' => auth()->user()->role_display]) }}</p>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-md border-l-4 border-green-600 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold">{{ __('My Courses') }}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ number_format($stats['total_courses']) }}</p>
                            <p class="text-gray-500 text-xs mt-1">
                                <i class="fas fa-book mr-1"></i>{{ __('Teaching') }}
                            </p>
                        </div>
                        <div class="bg-green-100 rounded-lg p-3">
                            <i class="fas fa-book text-2xl text-green-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border-l-4 border-blue-600 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold">{{ __('Total Students') }}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ number_format($stats['total_students']) }}</p>
                            <p class="text-gray-500 text-xs mt-1">
                                <i class="fas fa-user-graduate mr-1"></i>{{ __('Enrolled') }}
                            </p>
                        </div>
                        <div class="bg-blue-100 rounded-lg p-3">
                            <i class="fas fa-users text-2xl text-blue-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border-l-4 border-purple-600 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold">{{ __('Total Exams') }}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_exams']) }}
                            </p>
                            <p class="text-gray-500 text-xs mt-1">
                                <i class="fas fa-clipboard-list mr-1"></i>{{ __('Created') }}
                            </p>
                        </div>
                        <div class="bg-purple-100 rounded-lg p-3">
                            <i class="fas fa-clipboard-list text-2xl text-purple-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border-l-4 border-orange-600 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold">{{ __('Pending Reviews') }}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ number_format($stats['pending_essays']) }}</p>
                            <p class="text-gray-500 text-xs mt-1">
                                <i class="fas fa-pen mr-1"></i>{{ __('Essays') }}
                            </p>
                        </div>
                        <div class="bg-orange-100 rounded-lg p-3">
                            <i class="fas fa-tasks text-2xl text-orange-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h4 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-bolt text-yellow-500 mr-2"></i>{{ __('Quick Actions') }}
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.create') }}"
                        class="flex items-center p-4 bg-green-50 rounded-lg border border-green-200 hover:bg-green-100 hover:border-green-300 transition-all">
                        <div class="bg-green-600 rounded-lg p-3 text-white mr-3">
                            <i class="fas fa-book"></i>
                        </div>
                        <span class="text-gray-900 font-semibold">{{ __('Create Course') }}</span>
                    </a>
                    <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.index') }}"
                        class="flex items-center p-4 bg-purple-50 rounded-lg border border-purple-200 hover:bg-purple-100 hover:border-purple-300 transition-all">
                        <div class="bg-purple-600 rounded-lg p-3 text-white mr-3">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <span class="text-gray-900 font-semibold">{{ __('My Exams') }}</span>
                    </a>
                    <a href="{{ route(auth()->user()->getRolePrefix() . '.analytics.index') }}"
                        class="flex items-center p-4 bg-blue-50 rounded-lg border border-blue-200 hover:bg-blue-100 hover:border-blue-300 transition-all">
                        <div class="bg-blue-600 rounded-lg p-3 text-white mr-3">
                            <i class="fas fa-chart-area"></i>
                        </div>
                        <span class="text-gray-900 font-semibold">{{ __('Analytics') }}</span>
                    </a>
                    <a href="{{ route(auth()->user()->getRolePrefix() . '.reports.index') }}"
                        class="flex items-center p-4 bg-orange-50 rounded-lg border border-orange-200 hover:bg-orange-100 hover:border-orange-300 transition-all">
                        <div class="bg-orange-600 rounded-lg p-3 text-white mr-3">
                            <i class="fas fa-file-export"></i>
                        </div>
                        <span class="text-gray-900 font-semibold">{{ __('Reports') }}</span>
                    </a>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- My Courses -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-book-open text-green-600 mr-2"></i>{{ __('My Courses') }}
                        </h4>
                        <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.index') }}"
                            class="text-sm text-blue-600 hover:text-blue-800 font-semibold">{{ __('View all →') }}</a>
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
                            <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-plus mr-2"></i>{{ __('Create Course') }}
                            </a>
                        </div>
                    @endforelse
                </div>

                <!-- Upcoming Exams -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>{{ __('Upcoming Exams') }}
                        </h4>
                        <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.index') }}"
                            class="text-sm text-blue-600 hover:text-blue-800 font-semibold">{{ __('View all →') }}</a>
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
                                    {{ $exam->start_time ? $exam->start_time->translatedFormat('d M') : __('No date') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">{{ __('No upcoming exams') }}</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-history text-blue-600 mr-2"></i>{{ __('Recent Exam Submissions') }}
                    </h4>
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
                        <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.show', $attempt->exam) }}"
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
