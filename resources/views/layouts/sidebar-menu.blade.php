@php
    $user = Auth::user();
    $school = \App\Models\School::active()->first();
    $appName = $school ? $school->name : 'Learning Management System';
@endphp

<div class="flex h-full flex-col bg-white border-r border-gray-200 overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-5 flex items-center gap-3 border-b border-gray-200 flex-shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group min-w-0">
            <div
                class="h-10 w-10 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow flex-shrink-0 overflow-hidden">
                <x-application-logo class="h-10 w-10 object-contain" />
            </div>
            <div class="flex flex-col min-w-0 flex-1">
                <h1 class="text-base font-bold text-gray-900 leading-tight truncate">
                    {{ $appName }}
                </h1>
                <p class="text-xs font-medium text-gray-500 leading-tight truncate">
                    Platform Pembelajaran Digital
                </p>
            </div>
        </a>
        @if (($isMobile ?? false) === true)
            <button @click="sidebarOpen = false"
                class="ml-auto lg:hidden text-gray-500 hover:text-gray-700 transition-colors flex-shrink-0">
                <i class="fas fa-times text-lg"></i>
            </button>
        @endif
    </div>

    <!-- Scrollable Content -->
    <div class="flex-1 overflow-y-auto overflow-x-hidden py-6 custom-scrollbar">
        @if (!$user)
            <!-- Guest View -->
            <div class="px-4 text-center space-y-4">
                <div class="bg-blue-50 rounded-lg p-6 border border-blue-100">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-circle text-white text-2xl"></i>
                    </div>
                    <p class="text-sm text-gray-900 font-semibold mb-1">
                        {{ __('Mode Tamu') }}
                    </p>
                    <p class="text-xs text-gray-600">
                        {{ __('Silakan login untuk akses penuh.') }}
                    </p>
                </div>
                <a href="{{ route('login') }}"
                    class="flex w-full items-center justify-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-semibold shadow-sm hover:shadow-md">
                    <i class="fas fa-sign-in-alt text-sm"></i>
                    <span>{{ __('Log In') }}</span>
                </a>
            </div>
        @else
            <!-- Authenticated View -->
            <nav class="space-y-6">
                <!-- Main Section -->
                <div class="space-y-1">
                    <p class="px-6 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 font-display">
                        {{ __('Menu Utama') }}
                    </p>

                    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-home text-sm"></i>
                        </div>
                        <span class="truncate">{{ __('Dashboard') }}</span>
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('forum.index')" :active="request()->routeIs('forum.*')">
                        <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-comments text-sm"></i>
                        </div>
                        <span class="truncate">{{ __('Forum Diskusi') }}</span>
                    </x-sidebar-link>
                </div>

                <!-- Admin Section -->
                @if ($user->isAdmin())
                    @php
                        $adminManagementActive = request()->routeIs(
                            'admin.users.*',
                            'admin.classes.*',
                            'admin.courses.*',
                            'admin.exams.*',
                            'admin.questions.*',
                            'admin.question-bank.*',
                            'admin.question-bank-categories.*',
                            'admin.forum-categories.*',
                            'admin.authorization-logs.*',
                            'admin.cheating-incidents.*',
                            'admin.information-cards.*',
                        );
                        $adminReportsActive = request()->routeIs('admin.analytics.*');
                        $adminSettingsActive = request()->routeIs(
                            'admin.certificate-settings.*',
                            'admin.ai-settings.*',
                        );
                        $adminPlatformActive = request()->routeIs(
                            'admin.settings.*',
                            'admin.documentation.*',
                            'admin.certificate-settings.*',
                            'admin.ai-settings.*',
                        );
                    @endphp

                    <div class="space-y-1">
                        <p class="px-6 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 font-display">
                            {{ __('Administrasi') }}
                        </p>

                        <!-- Management Group -->
                        <x-sidebar-group :label="__('Manajemen')" icon="fas fa-tasks" :active="$adminManagementActive">
                            <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-users text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Pengguna') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.classes.index')" :active="request()->routeIs('admin.classes.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-chalkboard text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Kelas') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.courses.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-book text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Kursus') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.exams.index')" :active="request()->routeIs('admin.exams.*') || request()->routeIs('admin.questions.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-clipboard-list text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Ujian') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.question-bank.index')" :active="request()->routeIs('admin.question-bank.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-database text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Bank Soal') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.question-bank-categories.index')" :active="request()->routeIs('admin.question-bank-categories.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-folder-open text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Kategori Bank Soal') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.forum-categories.index')" :active="request()->routeIs('admin.forum-categories.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-tags text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Kategori Forum') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.authorization-logs.index')" :active="request()->routeIs('admin.authorization-logs.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-shield-alt text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Log Akses') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.cheating-incidents.index')" :active="request()->routeIs('admin.cheating-incidents.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-user-slash text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Pelanggaran') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.information-cards.index')" :active="request()->routeIs('admin.information-cards.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-id-card text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Kartu Informasi') }}</span>
                            </x-sidebar-link>
                        </x-sidebar-group>

                        <!-- Reports Group -->
                        <x-sidebar-group :label="__('Laporan')" icon="fas fa-chart-bar" :active="$adminReportsActive">
                            <x-sidebar-link :href="route('admin.analytics.index')" :active="request()->routeIs('admin.analytics.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-chart-area text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Analitik') }}</span>
                            </x-sidebar-link>
                        </x-sidebar-group>

                        <!-- Platform Group -->
                        <x-sidebar-group :label="__('Platform')" icon="fas fa-server" :active="$adminPlatformActive">
                            <x-sidebar-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*') ||
                                request()->routeIs('admin.schools.*') ||
                                request()->routeIs('admin.landing-page.*') ||
                                request()->routeIs('admin.schools.theme.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-sliders-h text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Pengaturan') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.certificate-settings.index')" :active="request()->routeIs('admin.certificate-settings.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-certificate text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Sertifikat') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.ai-settings.index')" :active="request()->routeIs('admin.ai-settings.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-robot text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Pengaturan AI') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.documentation.index')" :active="request()->routeIs('admin.documentation.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-book-reader text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Dokumentasi') }}</span>
                            </x-sidebar-link>
                        </x-sidebar-group>
                    </div>
                @elseif($user->isGuru() || $user->isDosen())
                    @php
                        $rolePrefix = $user->isDosen() ? 'dosen' : 'guru';
                        $guruContentActive = request()->routeIs(
                            "{$rolePrefix}.courses.*",
                            "{$rolePrefix}.exams.*",
                            "{$rolePrefix}.questions.*",
                            "{$rolePrefix}.question-bank.*",
                        );
                        $guruAnalyticsActive = request()->routeIs("{$rolePrefix}.analytics.*");
                        $guruReportsActive = request()->routeIs("{$rolePrefix}.reports.*");
                    @endphp

                    <div class="space-y-1">
                        <p class="px-6 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 font-display">
                            {{ __('Area Pengajar') }}
                        </p>

                        <x-sidebar-group :label="__('Konten Saya')" icon="fas fa-chalkboard-teacher" :active="$guruContentActive">
                            <x-sidebar-link :href="route($rolePrefix . '.courses.index')" :active="request()->routeIs($rolePrefix . '.courses.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-book-open text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Kursus Saya') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route($rolePrefix . '.exams.index')" :active="request()->routeIs($rolePrefix . '.exams.*') ||
                                request()->routeIs($rolePrefix . '.questions.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-file-alt text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Ujian Saya') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route($rolePrefix . '.question-bank.index')" :active="request()->routeIs($rolePrefix . '.question-bank.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-database text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Bank Soal') }}</span>
                            </x-sidebar-link>
                        </x-sidebar-group>

                        <x-sidebar-link :href="route($rolePrefix . '.analytics.index')" :active="$guruAnalyticsActive">
                            <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-chart-line text-sm"></i>
                            </div>
                            <span class="truncate">{{ __('Analitik Siswa') }}</span>
                        </x-sidebar-link>

                        <x-sidebar-link :href="route($rolePrefix . '.reports.index')" :active="$guruReportsActive">
                            <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-file-contract text-sm"></i>
                            </div>
                            <span class="truncate">{{ __('Laporan Nilai') }}</span>
                        </x-sidebar-link>

                        <x-sidebar-link :href="route($rolePrefix . '.information-cards.index')" :active="request()->routeIs($rolePrefix . '.information-cards.*')">
                            <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-id-card text-sm"></i>
                            </div>
                            <span class="truncate">{{ __('Kartu Informasi') }}</span>
                        </x-sidebar-link>
                    </div>
                @elseif($user->isSiswa() || $user->isMahasiswa())
                    @php
                        $rolePrefix = $user->isMahasiswa() ? 'mahasiswa' : 'siswa';
                        $studentCoursesActive = request()->routeIs("{$rolePrefix}.courses.*");
                        $studentExamsActive = request()->routeIs("{$rolePrefix}.exams.*", "{$rolePrefix}.reports.*");
                        $studentAnalyticsActive = request()->routeIs("{$rolePrefix}.analytics.*");
                    @endphp

                    <div class="space-y-1">
                        <p class="px-6 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 font-display">
                            {{ __('Pembelajaran') }}
                        </p>

                        <x-sidebar-group :label="__('Kursus')" icon="fas fa-graduation-cap" :active="$studentCoursesActive">
                            <x-sidebar-link :href="route($rolePrefix . '.courses.index')" :active="request()->routeIs($rolePrefix . '.courses.index')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-search text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Jelajahi Kursus') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route($rolePrefix . '.courses.my-courses')" :active="request()->routeIs($rolePrefix . '.courses.my-courses')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-book-reader text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Kursus Saya') }}</span>
                            </x-sidebar-link>
                        </x-sidebar-group>

                        <x-sidebar-group :label="__('Ujian & Nilai')" icon="fas fa-pen-alt" :active="$studentExamsActive">
                            <x-sidebar-link :href="route($rolePrefix . '.exams.index')" :active="request()->routeIs($rolePrefix . '.exams.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-edit text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Daftar Ujian') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route($rolePrefix . '.reports.index')" :active="request()->routeIs($rolePrefix . '.reports.*')">
                                <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-certificate text-sm"></i>
                                </div>
                                <span class="truncate">{{ __('Transkrip Nilai') }}</span>
                            </x-sidebar-link>
                        </x-sidebar-group>

                        <x-sidebar-link :href="route($rolePrefix . '.analytics.index')" :active="$studentAnalyticsActive">
                            <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-chart-pie text-sm"></i>
                            </div>
                            <span class="truncate">{{ __('Progres Belajar') }}</span>
                        </x-sidebar-link>
                    </div>
                @endif
            </nav>
        @endif
    </div>

    <!-- Footer Info -->
    <div class="px-6 py-4 text-xs text-gray-500 text-center border-t border-gray-200 flex-shrink-0">
        <p class="font-semibold">&copy; {{ date('Y') }} Learning Management System</p>
        <p class="text-gray-400">v1.1.10</p>
    </div>
</div>
