<nav x-data="{ open: false }" class="bg-white/95 backdrop-blur-sm border-b border-gray-200 shadow-sm sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 hover:opacity-80 transition-opacity">
                        <x-application-logo class="h-9 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <i class="fas fa-home mr-1"></i>{{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('forum.index')" :active="request()->routeIs('forum.*')">
                        <i class="fas fa-comments mr-1"></i>{{ __('Forum') }}
                    </x-nav-link>

                    @if (auth()->user()->isAdmin())
                        <!-- Admin Navigation with Sub Menus -->
                        <x-nav-dropdown align="left" width="56">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.courses.*') || request()->routeIs('admin.exams.*') || request()->routeIs('admin.questions.*') ? 'border-indigo-400 text-gray-900' : '' }}">
                                    <i class="fas fa-cog mr-1"></i>{{ __('Management') }}
                                    <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                    <i class="fas fa-users mr-2"></i>{{ __('Users') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.courses.*')">
                                    <i class="fas fa-book mr-2"></i>{{ __('Courses') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.exams.index')" :active="request()->routeIs('admin.exams.*') || request()->routeIs('admin.questions.*')">
                                    <i class="fas fa-clipboard-list mr-2"></i>{{ __('Exams') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.question-bank.index')" :active="request()->routeIs('admin.question-bank.*')">
                                    <i class="fas fa-database mr-2"></i>{{ __('Question Bank') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.forum-categories.index')" :active="request()->routeIs('admin.forum-categories.*')">
                                    <i class="fas fa-tags mr-2"></i>{{ __('Forum Categories') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.authorization-logs.index')" :active="request()->routeIs('admin.authorization-logs.*')">
                                    <i class="fas fa-shield-alt mr-2"></i>{{ __('Authorization Logs') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-nav-dropdown>

                        <x-nav-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out {{ request()->routeIs('admin.analytics.*') || request()->routeIs('admin.reports.*') ? 'border-indigo-400 text-gray-900' : '' }}">
                                    <i class="fas fa-chart-bar mr-1"></i>{{ __('Reports') }}
                                    <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('admin.analytics.index')" :active="request()->routeIs('admin.analytics.*')">
                                    <i class="fas fa-chart-area mr-2"></i>{{ __('Analytics') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-nav-dropdown>

                        <x-nav-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out {{ request()->routeIs('admin.certificate-settings.*') || request()->routeIs('admin.ai-settings.*') ? 'border-indigo-400 text-gray-900' : '' }}">
                                    <i class="fas fa-tools mr-1"></i>{{ __('Settings') }}
                                    <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('admin.certificate-settings.index')" :active="request()->routeIs('admin.certificate-settings.*')">
                                    <i class="fas fa-certificate mr-2"></i>{{ __('Certificates') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.ai-settings.index')" :active="request()->routeIs('admin.ai-settings.*')">
                                    <i class="fas fa-robot mr-2"></i>{{ __('AI Settings') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-nav-dropdown>
                    @elseif(auth()->user()->isGuru())
                        <!-- Guru Navigation with Sub Menus -->
                        <x-nav-dropdown align="left" width="56">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out {{ request()->routeIs('guru.courses.*') || request()->routeIs('guru.exams.*') || request()->routeIs('guru.questions.*') ? 'border-indigo-400 text-gray-900' : '' }}">
                                    <i class="fas fa-book mr-1"></i>{{ __('My Content') }}
                                    <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('guru.courses.index')" :active="request()->routeIs('guru.courses.*')">
                                    <i class="fas fa-book-open mr-2"></i>{{ __('My Courses') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('guru.exams.index')" :active="request()->routeIs('guru.exams.*') || request()->routeIs('guru.questions.*')">
                                    <i class="fas fa-clipboard-list mr-2"></i>{{ __('My Exams') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-nav-dropdown>

                        <x-nav-link :href="route('guru.analytics.index')" :active="request()->routeIs('guru.analytics.*')">
                            <i class="fas fa-chart-area mr-1"></i>{{ __('Analytics') }}
                        </x-nav-link>
                        <x-nav-link :href="route('guru.reports.index')" :active="request()->routeIs('guru.reports.*')">
                            <i class="fas fa-file-alt mr-1"></i>{{ __('Reports') }}
                        </x-nav-link>
                    @elseif(auth()->user()->isSiswa())
                        <!-- Siswa Navigation with Sub Menus -->
                        <x-nav-dropdown align="left" width="56">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out {{ request()->routeIs('siswa.courses.*') ? 'border-indigo-400 text-gray-900' : '' }}">
                                    <i class="fas fa-book mr-1"></i>{{ __('Courses') }}
                                    <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('siswa.courses.index')" :active="request()->routeIs('siswa.courses.index')">
                                    <i class="fas fa-search mr-2"></i>{{ __('Browse Courses') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('siswa.courses.my-courses')" :active="request()->routeIs('siswa.courses.my-courses')">
                                    <i class="fas fa-book-open mr-2"></i>{{ __('My Courses') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-nav-dropdown>

                        <x-nav-dropdown align="left" width="56">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out {{ request()->routeIs('siswa.exams.*') || request()->routeIs('siswa.reports.*') ? 'border-indigo-400 text-gray-900' : '' }}">
                                    <i class="fas fa-clipboard-list mr-1"></i>{{ __('Exams') }}
                                    <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('siswa.exams.index')" :active="request()->routeIs('siswa.exams.index') || request()->routeIs('siswa.exams.show')">
                                    <i class="fas fa-list mr-2"></i>{{ __('Available Exams') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('siswa.exams.my-attempts')" :active="request()->routeIs('siswa.exams.my-attempts')">
                                    <i class="fas fa-check-circle mr-2"></i>{{ __('My Results') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('siswa.reports.my-transcript')" :active="request()->routeIs('siswa.reports.*')">
                                    <i class="fas fa-file-alt mr-2"></i>{{ __('My Transcript') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-nav-dropdown>

                        <x-nav-link :href="route('siswa.analytics.index')" :active="request()->routeIs('siswa.analytics.*')">
                            <i class="fas fa-chart-line mr-1"></i>{{ __('Analytics') }}
                        </x-nav-link>
                        <x-nav-link :href="route('ai.index')" :active="request()->routeIs('ai.*')">
                            <i class="fas fa-robot mr-1"></i>{{ __('AI Assistant') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <!-- Notification Bell -->
                <x-notification-bell />

                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-gray-200 text-sm leading-4 font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-150 shadow-sm">
                            <div class="flex items-center">
                                <img class="h-8 w-8 rounded-full object-cover ring-2 ring-gray-200"
                                    src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                                <div class="ms-2 text-left hidden md:block">
                                    <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500">{{ Auth::user()->role_display }}</div>
                                </div>
                            </div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            <i class="fas fa-user mr-2"></i>{{ __('Profile') }}
                        </x-dropdown-link>

                        @if (auth()->user()->isAdmin())
                            <x-dropdown-link :href="route('admin.schools.index')">
                                <i class="fas fa-school mr-2"></i>{{ __('Schools') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('admin.settings.index')">
                                <i class="fas fa-cog mr-2"></i>{{ __('Settings') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('admin.documentation.index')">
                                <i class="fas fa-book mr-2"></i>{{ __('Documentation') }}
                            </x-dropdown-link>
                        @endif

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                <i class="fas fa-sign-out-alt mr-2"></i>{{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden" x-data="{ 
        managementOpen: false,
        reportsOpen: false,
        settingsOpen: false,
        coursesOpen: false,
        examsOpen: false
    }">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <i class="fas fa-home mr-2"></i>{{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('forum.index')" :active="request()->routeIs('forum.*')">
                <i class="fas fa-comments mr-2"></i>{{ __('Forum') }}
            </x-responsive-nav-link>

            @if (auth()->user()->isAdmin())
                <!-- Admin Mobile Navigation with Sub Menus -->
                <div class="border-t border-gray-200 mt-2 pt-2">
                    <button @click="managementOpen = !managementOpen" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                        <span><i class="fas fa-cog mr-2"></i>{{ __('Management') }}</span>
                        <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': managementOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="managementOpen" class="pl-4 space-y-1">
                        <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            <i class="fas fa-users mr-2"></i>{{ __('Users') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.courses.*')">
                            <i class="fas fa-book mr-2"></i>{{ __('Courses') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.exams.index')" :active="request()->routeIs('admin.exams.*') || request()->routeIs('admin.questions.*')">
                            <i class="fas fa-clipboard-list mr-2"></i>{{ __('Exams') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.question-bank.index')" :active="request()->routeIs('admin.question-bank.*')">
                            <i class="fas fa-database mr-2"></i>{{ __('Question Bank') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.forum-categories.index')" :active="request()->routeIs('admin.forum-categories.*')">
                            <i class="fas fa-tags mr-2"></i>{{ __('Forum Categories') }}
                        </x-responsive-nav-link>
                    </div>
                </div>

                <div class="border-t border-gray-200">
                    <button @click="reportsOpen = !reportsOpen" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                        <span><i class="fas fa-chart-bar mr-2"></i>{{ __('Reports') }}</span>
                        <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': reportsOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="reportsOpen" class="pl-4 space-y-1">
                        <x-responsive-nav-link :href="route('admin.analytics.index')" :active="request()->routeIs('admin.analytics.*')">
                            <i class="fas fa-chart-area mr-2"></i>{{ __('Analytics') }}
                        </x-responsive-nav-link>
                    </div>
                </div>

                <div class="border-t border-gray-200">
                    <button @click="settingsOpen = !settingsOpen" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                        <span><i class="fas fa-tools mr-2"></i>{{ __('Settings') }}</span>
                        <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': settingsOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="settingsOpen" class="pl-4 space-y-1">
                        <x-responsive-nav-link :href="route('admin.certificate-settings.index')" :active="request()->routeIs('admin.certificate-settings.*')">
                            <i class="fas fa-certificate mr-2"></i>{{ __('Certificates') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.ai-settings.index')" :active="request()->routeIs('admin.ai-settings.*')">
                            <i class="fas fa-robot mr-2"></i>{{ __('AI Settings') }}
                        </x-responsive-nav-link>
                    </div>
                </div>
            @elseif(auth()->user()->isGuru())
                <!-- Guru Mobile Navigation with Sub Menus -->
                <div class="border-t border-gray-200 mt-2 pt-2">
                    <button @click="coursesOpen = !coursesOpen" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                        <span><i class="fas fa-book mr-2"></i>{{ __('My Content') }}</span>
                        <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': coursesOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="coursesOpen" class="pl-4 space-y-1">
                        <x-responsive-nav-link :href="route('guru.courses.index')" :active="request()->routeIs('guru.courses.*')">
                            <i class="fas fa-book-open mr-2"></i>{{ __('My Courses') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('guru.exams.index')" :active="request()->routeIs('guru.exams.*') || request()->routeIs('guru.questions.*')">
                            <i class="fas fa-clipboard-list mr-2"></i>{{ __('My Exams') }}
                        </x-responsive-nav-link>
                    </div>
                </div>

                <x-responsive-nav-link :href="route('guru.analytics.index')" :active="request()->routeIs('guru.analytics.*')">
                    <i class="fas fa-chart-area mr-2"></i>{{ __('Analytics') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('guru.reports.index')" :active="request()->routeIs('guru.reports.*')">
                    <i class="fas fa-file-alt mr-2"></i>{{ __('Reports') }}
                </x-responsive-nav-link>
            @elseif(auth()->user()->isSiswa())
                <!-- Siswa Mobile Navigation with Sub Menus -->
                <div class="border-t border-gray-200 mt-2 pt-2">
                    <button @click="coursesOpen = !coursesOpen" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                        <span><i class="fas fa-book mr-2"></i>{{ __('Courses') }}</span>
                        <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': coursesOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="coursesOpen" class="pl-4 space-y-1">
                        <x-responsive-nav-link :href="route('siswa.courses.index')" :active="request()->routeIs('siswa.courses.index')">
                            <i class="fas fa-search mr-2"></i>{{ __('Browse Courses') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('siswa.courses.my-courses')" :active="request()->routeIs('siswa.courses.my-courses')">
                            <i class="fas fa-book-open mr-2"></i>{{ __('My Courses') }}
                        </x-responsive-nav-link>
                    </div>
                </div>

                <div class="border-t border-gray-200">
                    <button @click="examsOpen = !examsOpen" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                        <span><i class="fas fa-clipboard-list mr-2"></i>{{ __('Exams') }}</span>
                        <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': examsOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="examsOpen" class="pl-4 space-y-1">
                        <x-responsive-nav-link :href="route('siswa.exams.index')" :active="request()->routeIs('siswa.exams.index') || request()->routeIs('siswa.exams.show')">
                            <i class="fas fa-list mr-2"></i>{{ __('Available Exams') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('siswa.exams.my-attempts')" :active="request()->routeIs('siswa.exams.my-attempts')">
                            <i class="fas fa-check-circle mr-2"></i>{{ __('My Results') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('siswa.reports.my-transcript')" :active="request()->routeIs('siswa.reports.*')">
                            <i class="fas fa-file-alt mr-2"></i>{{ __('My Transcript') }}
                        </x-responsive-nav-link>
                    </div>
                </div>

                <x-responsive-nav-link :href="route('siswa.analytics.index')" :active="request()->routeIs('siswa.analytics.*')">
                    <i class="fas fa-chart-line mr-2"></i>{{ __('Analytics') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('ai.index')" :active="request()->routeIs('ai.*')">
                    <i class="fas fa-robot mr-2"></i>{{ __('AI Assistant') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="flex items-center">
                    <img class="h-10 w-10 rounded-full object-cover mr-3" src="{{ Auth::user()->profile_photo_url }}"
                        alt="{{ Auth::user()->name }}">
                    <div>
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                        <div class="text-xs text-gray-400">{{ Auth::user()->role_display }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    <i class="fas fa-user mr-2"></i>{{ __('Profile') }}
                </x-responsive-nav-link>

                @if (auth()->user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.schools.index')">
                        <i class="fas fa-school mr-2"></i>{{ __('Schools') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('admin.settings.index')">
                        <i class="fas fa-cog mr-2"></i>{{ __('Settings') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt mr-2"></i>{{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
