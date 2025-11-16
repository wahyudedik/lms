<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-tachometer-alt mr-2"></i>{{ __('Dashboard Administrator') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome Message -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <h3 class="text-2xl font-bold mb-2">{{ __('Selamat datang, :name! 👋', ['name' => auth()->user()->name]) }}
                </h3>
                <p class="text-blue-100">{{ __('Anda login sebagai :role', ['role' => auth()->user()->role_display]) }}</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm">{{ __('Total Users') }}</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_users']) }}</p>
                            <p class="text-blue-100 text-xs mt-1">
                                <i class="fas fa-user-check mr-1"></i>{{ number_format($stats['active_students']) }}
                                {{ __('active students') }}
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">{{ __('Total Courses') }}</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_courses']) }}</p>
                            <p class="text-green-100 text-xs mt-1">
                                <i class="fas fa-user-graduate mr-1"></i>{{ number_format($stats['total_enrollments']) }}
                                {{ __('enrollments') }}
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-book text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm">{{ __('Total Exams') }}</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_exams']) }}</p>
                            <p class="text-purple-100 text-xs mt-1">
                                <i class="fas fa-check-circle mr-1"></i>{{ number_format($stats['total_attempts']) }}
                                {{ __('attempts') }}
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
                            <p class="text-orange-100 text-sm">{{ __('Average Score') }}</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['avg_exam_score'], 1) }}%</p>
                            <p class="text-orange-100 text-xs mt-1">
                                <i class="fas fa-chart-line mr-1"></i>{{ __('System performance') }}
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-award text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4"><i
                        class="fas fa-bolt text-yellow-500 mr-2"></i>{{ __('Quick Actions') }}</h4>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <a href="{{ route('admin.users.create') }}"
                        class="flex items-center p-4 bg-blue-50 rounded-lg border-2 border-blue-200 hover:border-blue-400 hover:shadow-md transition-all">
                        <div class="bg-blue-500 rounded-full p-3 text-white mr-3">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <span class="text-gray-700 font-medium">{{ __('Add User') }}</span>
                    </a>
                    <a href="{{ route('admin.courses.create') }}"
                        class="flex items-center p-4 bg-green-50 rounded-lg border-2 border-green-200 hover:border-green-400 hover:shadow-md transition-all">
                        <div class="bg-green-500 rounded-full p-3 text-white mr-3">
                            <i class="fas fa-book"></i>
                        </div>
                        <span class="text-gray-700 font-medium">{{ __('Create Course') }}</span>
                    </a>
                    <a href="{{ route('admin.analytics.index') }}"
                        class="flex items-center p-4 bg-purple-50 rounded-lg border-2 border-purple-200 hover:border-purple-400 hover:shadow-md transition-all">
                        <div class="bg-purple-500 rounded-full p-3 text-white mr-3">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <span class="text-gray-700 font-medium">{{ __('View Analytics') }}</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}"
                        class="flex items-center p-4 bg-gray-50 rounded-lg border-2 border-gray-200 hover:border-gray-400 hover:shadow-md transition-all">
                        <div class="bg-gray-500 rounded-full p-3 text-white mr-3">
                            <i class="fas fa-cog"></i>
                        </div>
                        <span class="text-gray-700 font-medium">{{ __('Settings') }}</span>
                    </a>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Users -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900"><i
                                class="fas fa-user-plus text-blue-500 mr-2"></i>{{ __('Recent Users') }}</h4>
                        <a href="{{ route('admin.users.index') }}"
                            class="text-sm text-blue-600 hover:text-blue-800">{{ __('View all →') }}</a>
                    </div>
                    @forelse($recentUsers as $user)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg mb-2 hover:bg-gray-100 transition">
                            <img class="h-10 w-10 rounded-full object-cover mr-3" src="{{ $user->profile_photo_url }}"
                                alt="{{ $user->name }}">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $user->role === 'guru' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $user->role === 'siswa' ? 'bg-purple-100 text-purple-800' : '' }}">
                                {{ $user->role_display }}
                            </span>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">{{ __('No recent users') }}</p>
                    @endforelse
                </div>

                <!-- Active Exams -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900"><i
                                class="fas fa-clipboard-list text-purple-500 mr-2"></i>{{ __('Active Exams') }}</h4>
                        <a href="{{ route('admin.exams.index') }}"
                            class="text-sm text-blue-600 hover:text-blue-800">{{ __('View all →') }}</a>
                    </div>
                    @forelse($activeExams as $exam)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg mb-2 hover:bg-gray-100 transition">
                            <div class="bg-purple-100 rounded-full p-2 text-purple-600 mr-3">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $exam->title }}</p>
                                <p class="text-xs text-gray-500">{{ $exam->course->title }}</p>
                            </div>
                            <span class="text-xs text-gray-500">{{ trans_choice(__(':count question|:count questions'), $exam->questions_count, ['count' => $exam->questions_count]) }}</span>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">{{ __('No active exams') }}</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Courses and Attempts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Courses -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900"><i
                                class="fas fa-book-open text-green-500 mr-2"></i>{{ __('Recent Courses') }}</h4>
                        <a href="{{ route('admin.courses.index') }}"
                            class="text-sm text-blue-600 hover:text-blue-800">{{ __('View all →') }}</a>
                    </div>
                    @forelse($recentCourses as $course)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg mb-2 hover:bg-gray-100 transition">
                            <div class="bg-green-100 rounded-full p-2 text-green-600 mr-3">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $course->title }}</p>
                                <p class="text-xs text-gray-500">{{ __('By :name', ['name' => $course->instructor->name]) }}</p>
                            </div>
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ __(\Illuminate\Support\Str::headline($course->status)) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">{{ __('No recent courses') }}</p>
                    @endforelse
                </div>

                <!-- Recent Exam Attempts -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900"><i
                                class="fas fa-chart-bar text-orange-500 mr-2"></i>{{ __('Recent Exam Attempts') }}</h4>
                    </div>
                    @forelse($recentAttempts as $attempt)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg mb-2 hover:bg-gray-100 transition">
                            @php
                                $attemptUser = $attempt->user;
                                $displayName = $attemptUser?->name ?? ($attempt->guest_name ?? __('Guest'));
                                $avatarUrl = $attemptUser?->profile_photo_url ?? asset('images/avatars/default-avatar.png');
                            @endphp
                            <img class="h-8 w-8 rounded-full object-cover mr-3"
                                src="{{ $avatarUrl }}" alt="{{ $displayName }}">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $displayName }}</p>
                                <p class="text-xs text-gray-500">{{ $attempt->exam->title }}</p>
                            </div>
                            <div class="text-right">
                                <p
                                    class="text-sm font-bold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($attempt->score, 1) }}%</p>
                                <p class="text-xs text-gray-500">{{ $attempt->submitted_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">{{ __('No recent attempts') }}</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
