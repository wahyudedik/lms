<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-graduation-cap mr-2"></i>{{ __('Dashboard Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome Message -->
            <div class="bg-purple-600 rounded-lg shadow-md p-6 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold mb-1">
                            {{ __('Selamat datang, :name!', ['name' => auth()->user()->name]) }}</h3>
                        <p class="text-purple-100">
                            {{ __('Anda login sebagai :role', ['role' => auth()->user()->role_display]) }}</p>
                    </div>
                </div>
            </div>

            <!-- Information Cards -->
            @if ($informationCards->isNotEmpty())
                <x-information-cards :cards="$informationCards" />
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-md border-l-4 border-purple-600 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold">{{ __('Enrolled Courses') }}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ number_format($stats['enrolled_courses']) }}</p>
                            <p class="text-gray-500 text-xs mt-1">
                                <i class="fas fa-book-open mr-1"></i>{{ __('Active') }}
                            </p>
                        </div>
                        <div class="bg-purple-100 rounded-lg p-3">
                            <i class="fas fa-book-open text-2xl text-purple-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border-l-4 border-green-600 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold">{{ __('Completed') }}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ number_format($stats['completed_courses']) }}</p>
                            <p class="text-gray-500 text-xs mt-1">
                                <i class="fas fa-check-circle mr-1"></i>{{ __('Courses') }}
                            </p>
                        </div>
                        <div class="bg-green-100 rounded-lg p-3">
                            <i class="fas fa-check-circle text-2xl text-green-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border-l-4 border-yellow-600 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold">{{ __('Pending Exams') }}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ number_format($stats['pending_exams']) }}</p>
                            <p class="text-gray-500 text-xs mt-1">
                                <i class="fas fa-clock mr-1"></i>{{ __('To Do') }}
                            </p>
                        </div>
                        <div class="bg-yellow-100 rounded-lg p-3">
                            <i class="fas fa-clock text-2xl text-yellow-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border-l-4 border-blue-600 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold">{{ __('Average Score') }}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ $stats['avg_score'] > 0 ? number_format($stats['avg_score'], 1) . '%' : '-' }}</p>
                            <p class="text-gray-500 text-xs mt-1">
                                <i class="fas fa-star mr-1"></i>{{ __('Performance') }}
                            </p>
                        </div>
                        <div class="bg-blue-100 rounded-lg p-3">
                            <i class="fas fa-star text-2xl text-blue-600"></i>
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
                    <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.index') }}"
                        class="flex items-center p-4 bg-purple-50 rounded-lg border border-purple-200 hover:bg-purple-100 hover:border-purple-300 transition-all">
                        <div class="bg-purple-600 rounded-lg p-3 text-white mr-3">
                            <i class="fas fa-search"></i>
                        </div>
                        <span class="text-gray-900 font-semibold">{{ __('Browse Courses') }}</span>
                    </a>
                    <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.my-courses') }}"
                        class="flex items-center p-4 bg-blue-50 rounded-lg border border-blue-200 hover:bg-blue-100 hover:border-blue-300 transition-all">
                        <div class="bg-blue-600 rounded-lg p-3 text-white mr-3">
                            <i class="fas fa-book"></i>
                        </div>
                        <span class="text-gray-900 font-semibold">{{ __('My Courses') }}</span>
                    </a>
                    <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.index') }}"
                        class="flex items-center p-4 bg-green-50 rounded-lg border border-green-200 hover:bg-green-100 hover:border-green-300 transition-all">
                        <div class="bg-green-600 rounded-lg p-3 text-white mr-3">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <span class="text-gray-900 font-semibold">{{ __('Exams') }}</span>
                    </a>
                    <a href="{{ route(auth()->user()->getRolePrefix() . '.analytics.index') }}"
                        class="flex items-center p-4 bg-orange-50 rounded-lg border border-orange-200 hover:bg-orange-100 hover:border-orange-300 transition-all">
                        <div class="bg-orange-600 rounded-lg p-3 text-white mr-3">
                            <i class="fas fa-poll"></i>
                        </div>
                        <span class="text-gray-900 font-semibold">{{ __('Analytics') }}</span>
                    </a>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- My Courses -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-book-open text-purple-600 mr-2"></i>{{ __('My Courses') }}
                        </h4>
                        <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.my-courses') }}"
                            class="text-sm text-blue-600 hover:text-blue-800 font-semibold">{{ __('View all →') }}</a>
                    </div>
                    @forelse($myCourses as $course)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg mb-2 hover:bg-gray-100 transition">
                            <div class="bg-purple-100 rounded-full p-2 text-purple-600 mr-3">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $course->title }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ __('By :name', ['name' => $course->instructor->name]) }}</p>
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
                            <p class="text-gray-500 mb-3">{{ __('No courses enrolled') }}</p>
                            <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                <i class="fas fa-search mr-2"></i>{{ __('Browse Courses') }}
                            </a>
                        </div>
                    @endforelse
                </div>

                <!-- Upcoming Exams -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-calendar-alt text-green-600 mr-2"></i>{{ __('Upcoming Exams') }}
                        </h4>
                        <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.index') }}"
                            class="text-sm text-blue-600 hover:text-blue-800 font-semibold">{{ __('View all →') }}</a>
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
                                    {{ $exam->start_time ? $exam->start_time->translatedFormat('d M') : __('Available') }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ trans_choice(__(':count minute|:count minutes'), $exam->duration, ['count' => $exam->duration]) }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">{{ __('No upcoming exams') }}</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Grades -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-chart-bar text-blue-600 mr-2"></i>{{ __('Recent Grades') }}
                    </h4>
                    <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.my-attempts') }}"
                        class="text-sm text-blue-600 hover:text-blue-800 font-semibold">{{ __('View all →') }}</a>
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
                        <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.review-attempt', $attempt) }}"
                            class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-clipboard text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-500 mb-3">{{ __('No exam results yet') }}</p>
                        <p class="text-xs text-gray-400">{{ __('Complete your first exam to see your grades here') }}
                        </p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
