@php
    $user = Auth::user();
@endphp

@if (!$user)
    <div class="flex h-full flex-col">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <x-application-logo class="h-10 w-auto" />
                <div>
                    <p class="text-base font-semibold text-gray-900">{{ config('app.name', 'LMS') }}</p>
                    <p class="text-xs text-gray-500">{{ __('Guest Mode') }}</p>
                </div>
            </a>
        </div>

        <div class="flex-1 flex flex-col items-center justify-center px-6 text-center space-y-3">
            <p class="text-sm text-gray-600">
                {{ __('Anda sedang mengakses ujian tamu. Login untuk membuka menu lengkap.') }}
            </p>
            <a href="{{ route('login') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                <i class="fas fa-sign-in-alt text-sm"></i>
                <span>{{ __('Log In') }}</span>
            </a>
        </div>
    </div>
@else
<div class="flex h-full flex-col">
    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <x-application-logo class="h-10 w-auto" />
            <div>
                <p class="text-base font-semibold text-gray-900">{{ config('app.name', 'LMS') }}</p>
                <p class="text-xs text-gray-500">{{ $user->role_display }}</p>
            </div>
        </a>

        @if (($isMobile ?? false) === true)
            <button @click="sidebarOpen = false" class="rounded-full p-2 text-gray-500 hover:bg-gray-100 transition">
                <i class="fas fa-times"></i>
            </button>
        @endif
    </div>

    <div class="flex-1 overflow-y-auto px-4 py-6 space-y-6">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ __('Main') }}</p>
            <div class="mt-3 space-y-1">
                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <i class="fas fa-home text-sm"></i>
                    <span>{{ __('Dashboard') }}</span>
                </x-sidebar-link>

                <x-sidebar-link :href="route('forum.index')" :active="request()->routeIs('forum.*')">
                    <i class="fas fa-comments text-sm"></i>
                    <span>{{ __('Forum') }}</span>
                </x-sidebar-link>
            </div>
        </div>

        @if ($user->isAdmin())
            @php
                $adminManagementActive = request()->routeIs('admin.users.*', 'admin.courses.*', 'admin.exams.*', 'admin.questions.*', 'admin.question-bank.*', 'admin.forum-categories.*', 'admin.authorization-logs.*', 'admin.cheating-incidents.*');
                $adminReportsActive = request()->routeIs('admin.analytics.*');
                $adminSettingsActive = request()->routeIs('admin.certificate-settings.*', 'admin.ai-settings.*');
                $adminPlatformActive = request()->routeIs('admin.schools.*', 'admin.settings.*', 'admin.documentation.*');
            @endphp
            <div class="pt-2 border-t border-gray-100">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ __('Administration') }}</p>
                <div class="mt-4 space-y-3">
                    <div x-data="{ open: {{ $adminManagementActive ? 'true' : 'false' }} }" class="space-y-2">
                        <button type="button"
                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg text-sm font-semibold transition {{ $adminManagementActive ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}"
                            @click="open = !open">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-cog text-sm"></i>{{ __('Management') }}
                            </span>
                            <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': open }"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="space-y-1 ps-3">
                            <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                <i class="fas fa-users text-sm"></i>
                                <span>{{ __('Users') }}</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.courses.*')">
                                <i class="fas fa-book text-sm"></i>
                                <span>{{ __('Courses') }}</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.exams.index')" :active="request()->routeIs('admin.exams.*') || request()->routeIs('admin.questions.*')">
                                <i class="fas fa-clipboard-list text-sm"></i>
                                <span>{{ __('Exams') }}</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.question-bank.index')" :active="request()->routeIs('admin.question-bank.*')">
                                <i class="fas fa-database text-sm"></i>
                                <span>{{ __('Question Bank') }}</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.forum-categories.index')" :active="request()->routeIs('admin.forum-categories.*')">
                                <i class="fas fa-tags text-sm"></i>
                                <span>{{ __('Forum Categories') }}</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.authorization-logs.index')" :active="request()->routeIs('admin.authorization-logs.*')">
                                <i class="fas fa-shield-alt text-sm"></i>
                                <span>{{ __('Authorization Logs') }}</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.cheating-incidents.index')" :active="request()->routeIs('admin.cheating-incidents.*')">
                                <i class="fas fa-user-slash text-sm"></i>
                                <span>{{ __('Cheating Incidents') }}</span>
                            </x-sidebar-link>
                        </div>
                    </div>

                    <div x-data="{ open: {{ $adminReportsActive ? 'true' : 'false' }} }" class="space-y-2">
                        <button type="button"
                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg text-sm font-semibold transition {{ $adminReportsActive ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}"
                            @click="open = !open">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-chart-bar text-sm"></i>{{ __('Reports') }}
                            </span>
                            <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': open }"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="space-y-1 ps-3">
                            <x-sidebar-link :href="route('admin.analytics.index')" :active="request()->routeIs('admin.analytics.*')">
                                <i class="fas fa-chart-area text-sm"></i>
                                <span>{{ __('Analytics') }}</span>
                            </x-sidebar-link>
                        </div>
                    </div>

                    <div x-data="{ open: {{ $adminSettingsActive ? 'true' : 'false' }} }" class="space-y-2">
                        <button type="button"
                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg text-sm font-semibold transition {{ $adminSettingsActive ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}"
                            @click="open = !open">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-tools text-sm"></i>{{ __('Settings') }}
                            </span>
                            <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': open }"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="space-y-1 ps-3">
                            <x-sidebar-link :href="route('admin.certificate-settings.index')" :active="request()->routeIs('admin.certificate-settings.*')">
                                <i class="fas fa-certificate text-sm"></i>
                                <span>{{ __('Certificates') }}</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.ai-settings.index')" :active="request()->routeIs('admin.ai-settings.*')">
                                <i class="fas fa-robot text-sm"></i>
                                <span>{{ __('AI Settings') }}</span>
                            </x-sidebar-link>
                        </div>
                    </div>

                    <div x-data="{ open: {{ $adminPlatformActive ? 'true' : 'false' }} }" class="space-y-2">
                        <button type="button"
                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg text-sm font-semibold transition {{ $adminPlatformActive ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}"
                            @click="open = !open">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-building text-sm"></i>{{ __('Platform') }}
                            </span>
                            <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': open }"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="space-y-1 ps-3">
                            <x-sidebar-link :href="route('admin.schools.index')" :active="request()->routeIs('admin.schools.*')">
                                <i class="fas fa-school text-sm"></i>
                                <span>{{ __('Schools') }}</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')">
                                <i class="fas fa-sliders-h text-sm"></i>
                                <span>{{ __('Settings') }}</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.documentation.index')" :active="request()->routeIs('admin.documentation.*')">
                                <i class="fas fa-book text-sm"></i>
                                <span>{{ __('Documentation') }}</span>
                            </x-sidebar-link>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($user->isGuru())
            @php
                $guruContentActive = request()->routeIs('guru.courses.*', 'guru.exams.*', 'guru.questions.*');
                $guruAnalyticsActive = request()->routeIs('guru.analytics.*');
                $guruReportsActive = request()->routeIs('guru.reports.*');
            @endphp
            <div class="pt-2 border-t border-gray-100">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ __('My Teaching') }}</p>
                <div class="mt-4 space-y-3">
                    <div x-data="{ open: {{ $guruContentActive ? 'true' : 'false' }} }" class="space-y-2">
                        <button type="button"
                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg text-sm font-semibold transition {{ $guruContentActive ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}"
                            @click="open = !open">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-book text-sm"></i>{{ __('My Content') }}
                            </span>
                            <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': open }"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="space-y-1 ps-3">
                            <x-sidebar-link :href="route('guru.courses.index')" :active="request()->routeIs('guru.courses.*')">
                                <i class="fas fa-book-open text-sm"></i>
                                <span>{{ __('My Courses') }}</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('guru.exams.index')" :active="request()->routeIs('guru.exams.*') || request()->routeIs('guru.questions.*')">
                                <i class="fas fa-clipboard-list text-sm"></i>
                                <span>{{ __('My Exams') }}</span>
                            </x-sidebar-link>
                        </div>
                    </div>

                    <x-sidebar-link :href="route('guru.analytics.index')" :active="$guruAnalyticsActive">
                        <i class="fas fa-chart-area text-sm"></i>
                        <span>{{ __('Analytics') }}</span>
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('guru.reports.index')" :active="$guruReportsActive">
                        <i class="fas fa-file-alt text-sm"></i>
                        <span>{{ __('Reports') }}</span>
                    </x-sidebar-link>
                </div>
            </div>
        @elseif($user->isSiswa())
            @php
                $studentCoursesActive = request()->routeIs('siswa.courses.*');
                $studentExamsActive = request()->routeIs('siswa.exams.*', 'siswa.reports.*');
                $studentAnalyticsActive = request()->routeIs('siswa.analytics.*');
                $studentAiActive = request()->routeIs('ai.*');
            @endphp
            <div class="pt-2 border-t border-gray-100">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ __('Learning') }}</p>
                <div class="mt-4 space-y-3">
                    <div x-data="{ open: {{ $studentCoursesActive ? 'true' : 'false' }} }" class="space-y-2">
                        <button type="button"
                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg text-sm font-semibold transition {{ $studentCoursesActive ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}"
                            @click="open = !open">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-book text-sm"></i>{{ __('Courses') }}
                            </span>
                            <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': open }"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="space-y-1 ps-3">
                            <x-sidebar-link :href="route('siswa.courses.index')" :active="request()->routeIs('siswa.courses.index')">
                                <i class="fas fa-search text-sm"></i>
                                <span>{{ __('Browse Courses') }}</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('siswa.courses.my-courses')" :active="request()->routeIs('siswa.courses.my-courses')">
                                <i class="fas fa-book-open text-sm"></i>
                                <span>{{ __('My Courses') }}</span>
                            </x-sidebar-link>
                        </div>
                    </div>

                    <div x-data="{ open: {{ $studentExamsActive ? 'true' : 'false' }} }" class="space-y-2">
                        <button type="button"
                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg text-sm font-semibold transition {{ $studentExamsActive ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50' }}"
                            @click="open = !open">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-clipboard-list text-sm"></i>{{ __('Exams') }}
                            </span>
                            <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': open }"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="space-y-1 ps-3">
                            <x-sidebar-link :href="route('siswa.exams.index')" :active="request()->routeIs('siswa.exams.index') || request()->routeIs('siswa.exams.show')">
                                <i class="fas fa-list text-sm"></i>
                                <span>{{ __('Available Exams') }}</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('siswa.exams.my-attempts')" :active="request()->routeIs('siswa.exams.my-attempts')">
                                <i class="fas fa-check-circle text-sm"></i>
                                <span>{{ __('My Results') }}</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('siswa.reports.my-transcript')" :active="request()->routeIs('siswa.reports.*')">
                                <i class="fas fa-file-alt text-sm"></i>
                                <span>{{ __('My Transcript') }}</span>
                            </x-sidebar-link>
                        </div>
                    </div>

                    <x-sidebar-link :href="route('siswa.analytics.index')" :active="$studentAnalyticsActive">
                        <i class="fas fa-chart-line text-sm"></i>
                        <span>{{ __('Analytics') }}</span>
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('ai.index')" :active="$studentAiActive">
                        <i class="fas fa-robot text-sm"></i>
                        <span>{{ __('AI Assistant') }}</span>
                    </x-sidebar-link>
                </div>
            </div>
        @endif
    </div>

    <div class="border-t border-gray-100 px-4 py-5 space-y-3">
        <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-gray-50">
            <img class="h-10 w-10 rounded-full object-cover ring-2 ring-gray-200" src="{{ $user->profile_photo_url }}"
                alt="{{ $user->name }}">
            <div>
                <p class="font-semibold text-gray-900 text-sm">{{ $user->name }}</p>
                <p class="text-xs text-gray-500">{{ $user->email }}</p>
            </div>
        </div>

        <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
            <i class="fas fa-user text-sm"></i>
            <span>{{ __('Profile') }}</span>
        </x-sidebar-link>

        <form method="POST" action="{{ route('logout') }}" class="space-y-0">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-red-600 rounded-lg border border-transparent hover:bg-red-50 hover:border-red-200 transition">
                <i class="fas fa-sign-out-alt text-sm"></i>
                <span>{{ __('Log Out') }}</span>
            </button>
        </form>
    </div>
</div>
@endif
