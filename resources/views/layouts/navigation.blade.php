<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <!-- Forum (All Users) -->
                    <x-nav-link :href="route('forum.index')" :active="request()->routeIs('forum.*')">
                        <i class="fas fa-comments mr-1"></i>{{ __('Forum') }}
                    </x-nav-link>

                    @if (auth()->user()->isAdmin())
                        <!-- Admin Navigation -->
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Users') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.courses.*')">
                            {{ __('Courses') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.exams.index')" :active="request()->routeIs('admin.exams.*') || request()->routeIs('admin.questions.*')">
                            {{ __('Exams') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.question-bank.index')" :active="request()->routeIs('admin.question-bank.*')">
                            <i class="fas fa-database mr-1"></i>{{ __('Q-Bank') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.analytics.index')" :active="request()->routeIs('admin.analytics.*')">
                            <i class="fas fa-chart-line mr-1"></i>{{ __('Analytics') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.forum-categories.index')" :active="request()->routeIs('admin.forum-categories.*')">
                            <i class="fas fa-folder mr-1"></i>{{ __('Forum') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.schools.index')" :active="request()->routeIs('admin.schools.*') || request()->routeIs('admin.theme.*')">
                            <i class="fas fa-school mr-1"></i>{{ __('Schools') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')">
                            {{ __('Settings') }}
                        </x-nav-link>
                    @elseif(auth()->user()->isGuru())
                        <!-- Guru Navigation -->
                        <x-nav-link :href="route('guru.courses.index')" :active="request()->routeIs('guru.courses.*')">
                            {{ __('My Courses') }}
                        </x-nav-link>
                        <x-nav-link :href="route('guru.exams.index')" :active="request()->routeIs('guru.exams.*') || request()->routeIs('guru.questions.*')">
                            {{ __('My Exams') }}
                        </x-nav-link>
                        <x-nav-link :href="route('guru.analytics.index')" :active="request()->routeIs('guru.analytics.*')">
                            <i class="fas fa-chart-area mr-1"></i>{{ __('Analytics') }}
                        </x-nav-link>
                        <x-nav-link :href="route('guru.reports.index')" :active="request()->routeIs('guru.reports.*')">
                            {{ __('Reports') }}
                        </x-nav-link>
                    @elseif(auth()->user()->isSiswa())
                        <!-- Siswa Navigation -->
                        <x-nav-link :href="route('siswa.courses.index')" :active="request()->routeIs('siswa.courses.index')">
                            {{ __('Browse Courses') }}
                        </x-nav-link>
                        <x-nav-link :href="route('siswa.courses.my-courses')" :active="request()->routeIs('siswa.courses.my-courses')">
                            {{ __('My Courses') }}
                        </x-nav-link>
                        <x-nav-link :href="route('siswa.exams.index')" :active="request()->routeIs('siswa.exams.*') &&
                            !request()->routeIs('siswa.exams.my-attempts')">
                            {{ __('Exams') }}
                        </x-nav-link>
                        <x-nav-link :href="route('siswa.analytics.index')" :active="request()->routeIs('siswa.analytics.*')">
                            <i class="fas fa-poll mr-1"></i>{{ __('Analytics') }}
                        </x-nav-link>
                        <x-nav-link :href="route('siswa.exams.my-attempts')" :active="request()->routeIs('siswa.exams.my-attempts')">
                            {{ __('My Results') }}
                        </x-nav-link>
                        <x-nav-link :href="route('siswa.reports.my-transcript')" :active="request()->routeIs('siswa.reports.*')">
                            {{ __('My Transcript') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                <!-- Notification Bell -->
                <x-notification-bell />

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center">
                                <img class="h-8 w-8 rounded-full object-cover mr-2"
                                    src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                                <div>
                                    <div>{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-400">{{ Auth::user()->role_display }}</div>
                                </div>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
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
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
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
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if (auth()->user()->isAdmin())
                <!-- Admin Mobile Navigation -->
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    {{ __('User Management') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.courses.*')">
                    {{ __('Course Management') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.exams.index')" :active="request()->routeIs('admin.exams.*')">
                    {{ __('Exam Management') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')">
                    {{ __('Settings') }}
                </x-responsive-nav-link>
            @elseif(auth()->user()->isGuru())
                <!-- Guru Mobile Navigation -->
                <x-responsive-nav-link :href="route('guru.courses.index')" :active="request()->routeIs('guru.courses.*')">
                    {{ __('My Courses') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('guru.exams.index')" :active="request()->routeIs('guru.exams.*') || request()->routeIs('guru.questions.*')">
                    {{ __('My Exams') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('guru.reports.index')" :active="request()->routeIs('guru.reports.*')">
                    {{ __('Reports') }}
                </x-responsive-nav-link>
            @elseif(auth()->user()->isSiswa())
                <!-- Siswa Mobile Navigation -->
                <x-responsive-nav-link :href="route('siswa.courses.index')" :active="request()->routeIs('siswa.courses.index')">
                    {{ __('Browse Courses') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('siswa.courses.my-courses')" :active="request()->routeIs('siswa.courses.my-courses')">
                    {{ __('My Courses') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('siswa.exams.index')" :active="request()->routeIs('siswa.exams.*') && !request()->routeIs('siswa.exams.my-attempts')">
                    {{ __('Exams') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('siswa.exams.my-attempts')" :active="request()->routeIs('siswa.exams.my-attempts')">
                    {{ __('My Results') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('siswa.reports.my-transcript')" :active="request()->routeIs('siswa.reports.*')">
                    {{ __('My Transcript') }}
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
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
