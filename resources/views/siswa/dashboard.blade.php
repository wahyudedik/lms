<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-graduation-cap mr-2"></i>{{ __('Dashboard Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome Message -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <h3 class="text-2xl font-bold mb-2">Selamat datang, {{ auth()->user()->name }}! 🎓</h3>
                <p class="text-purple-100">Anda login sebagai <span
                        class="font-semibold">{{ auth()->user()->role_display }}</span></p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm">Enrolled Courses</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['enrolled_courses']) }}</p>
                            <p class="text-purple-100 text-xs mt-1">
                                <i class="fas fa-book-open mr-1"></i>Active
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-book-open text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">Completed</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['completed_courses']) }}</p>
                            <p class="text-green-100 text-xs mt-1">
                                <i class="fas fa-check-circle mr-1"></i>Courses
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-100 text-sm">Pending Exams</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['pending_exams']) }}</p>
                            <p class="text-yellow-100 text-xs mt-1">
                                <i class="fas fa-clock mr-1"></i>To Do
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-clock text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm">Average Score</p>
                            <p class="text-3xl font-bold mt-2">
                                {{ $stats['avg_score'] > 0 ? number_format($stats['avg_score'], 1) . '%' : '-' }}</p>
                            <p class="text-blue-100 text-xs mt-1">
                                <i class="fas fa-star mr-1"></i>Performance
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-star text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4"><i
                        class="fas fa-bolt text-yellow-500 mr-2"></i>Quick
                    Actions</h4>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <a href="{{ route('siswa.courses.index') }}"
                        class="flex items-center p-4 bg-purple-50 rounded-lg border-2 border-purple-200 hover:border-purple-400 hover:shadow-md transition-all">
                        <div class="bg-purple-500 rounded-full p-3 text-white mr-3">
                            <i class="fas fa-search"></i>
                        </div>
                        <span class="text-gray-700 font-medium">Browse Courses</span>
                    </a>
                    <a href="{{ route('siswa.courses.my-courses') }}"
                        class="flex items-center p-4 bg-blue-50 rounded-lg border-2 border-blue-200 hover:border-blue-400 hover:shadow-md transition-all">
                        <div class="bg-blue-500 rounded-full p-3 text-white mr-3">
                            <i class="fas fa-book"></i>
                        </div>
                        <span class="text-gray-700 font-medium">My Courses</span>
                    </a>
                    <a href="{{ route('siswa.exams.index') }}"
                        class="flex items-center p-4 bg-green-50 rounded-lg border-2 border-green-200 hover:border-green-400 hover:shadow-md transition-all">
                        <div class="bg-green-500 rounded-full p-3 text-white mr-3">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <span class="text-gray-700 font-medium">Exams</span>
                    </a>
                    <a href="{{ route('siswa.analytics.index') }}"
                        class="flex items-center p-4 bg-orange-50 rounded-lg border-2 border-orange-200 hover:border-orange-400 hover:shadow-md transition-all">
                        <div class="bg-orange-500 rounded-full p-3 text-white mr-3">
                            <i class="fas fa-poll"></i>
                        </div>
                        <span class="text-gray-700 font-medium">Analytics</span>
                    </a>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- My Courses -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900"><i
                                class="fas fa-book-open text-purple-500 mr-2"></i>My Courses</h4>
                        <a href="{{ route('siswa.courses.my-courses') }}"
                            class="text-sm text-blue-600 hover:text-blue-800">View all →</a>
                    </div>
                    @forelse($myCourses as $course)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg mb-2 hover:bg-gray-100 transition">
                            <div class="bg-purple-100 rounded-full p-2 text-purple-600 mr-3">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $course->title }}</p>
                                <p class="text-xs text-gray-500">By {{ $course->instructor->name }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500 mb-1">{{ number_format($course->pivot->progress) }}%
                                </div>
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full"
                                        style="width: {{ $course->pivot->progress }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-book text-gray-400 text-4xl mb-3"></i>
                            <p class="text-gray-500 mb-3">No courses enrolled</p>
                            <a href="{{ route('siswa.courses.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                <i class="fas fa-search mr-2"></i>Browse Courses
                            </a>
                        </div>
                    @endforelse
                </div>

                <!-- Upcoming Exams -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900"><i
                                class="fas fa-calendar-alt text-green-500 mr-2"></i>Upcoming Exams</h4>
                        <a href="{{ route('siswa.exams.index') }}"
                            class="text-sm text-blue-600 hover:text-blue-800">View
                            all →</a>
                    </div>
                    @forelse($upcomingExams as $exam)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg mb-2 hover:bg-gray-100 transition">
                            <div class="bg-green-100 rounded-full p-2 text-green-600 mr-3">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $exam->title }}</p>
                                <p class="text-xs text-gray-500">{{ $exam->course->title }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold text-green-600">
                                    {{ $exam->start_time ? $exam->start_time->format('d M') : 'Available' }}</p>
                                <p class="text-xs text-gray-500">{{ $exam->duration }} min</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">No upcoming exams</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Grades -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900"><i
                            class="fas fa-chart-bar text-blue-500 mr-2"></i>Recent Grades</h4>
                    <a href="{{ route('siswa.exams.my-attempts') }}"
                        class="text-sm text-blue-600 hover:text-blue-800">View all →</a>
                </div>
                @forelse($recentGrades as $attempt)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg mb-2 hover:bg-gray-100 transition">
                        <div
                            class="rounded-full p-2 mr-3 {{ $attempt->passed ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                            <i class="fas {{ $attempt->passed ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $attempt->exam->title }}</p>
                            <p class="text-xs text-gray-500">{{ $attempt->exam->course->title }}</p>
                        </div>
                        <div class="text-right mr-4">
                            <p class="text-sm font-bold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($attempt->score, 1) }}%</p>
                            <p class="text-xs text-gray-500">{{ $attempt->submitted_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('siswa.exams.review-attempt', $attempt) }}"
                            class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-clipboard text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-500 mb-3">No exam results yet</p>
                        <p class="text-xs text-gray-400">Complete your first exam to see your grades here</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
