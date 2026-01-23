@php
    $user = Auth::user();
    $school = \App\Models\School::active()->first();
    $appName = $school ? $school->name : 'Koneksi';
@endphp

<div class="flex h-full flex-col bg-white">
    <!-- Header -->
    <div
        class="px-6 py-6 flex items-center gap-3 border-b border-gray-100/50 sticky top-0 bg-white/95 backdrop-blur z-10">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <x-application-logo
                class="h-9 w-9 shrink-0 group-hover:scale-105 transition-transform duration-200" />
            <div class="flex flex-col min-w-0">
                <h1
                    class="text-lg font-bold text-gray-900 leading-none truncate font-display tracking-tight group-hover:text-indigo-600 transition-colors">
                    {{ $appName }}
                </h1>
                <p
                    class="text-[10px] font-medium text-gray-500 leading-tight mt-1 truncate group-hover:text-gray-600 transition-colors">
                    Kolaborasi Online Edukasi dan Komunikasi Siswa
                </p>
            </div>
        </a>
        @if (($isMobile ?? false) === true)
            <button @click="sidebarOpen = false" class="ml-auto lg:hidden text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-lg"></i>
            </button>
        @endif
    </div>

    <!-- Scrollable Content -->
    <div class="flex-1 overflow-y-auto py-6 custom-scrollbar">
        @if (!$user)
            <!-- Guest View -->
            <div class="px-4 text-center space-y-4">
                <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                    <p class="text-sm text-indigo-900 font-medium">
                        {{ __('Mode Tamu') }}
                    </p>
                    <p class="text-xs text-indigo-700 mt-1">
                        {{ __('Silakan login untuk akses penuh.') }}
                    </p>
                </div>
                <a href="{{ route('login') }}"
                    class="flex w-full items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium shadow-sm shadow-indigo-200">
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
                        <div class="w-5 h-5 flex items-center justify-center">
                            <i class="fas fa-home text-sm"></i>
                        </div>
                        <span>{{ __('Dashboard') }}</span>
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('forum.index')" :active="request()->routeIs('forum.*')">
                        <div class="w-5 h-5 flex items-center justify-center">
                            <i class="fas fa-comments text-sm"></i>
                        </div>
                        <span>{{ __('Forum Diskusi') }}</span>
                    </x-sidebar-link>
                </div>

                <!-- Admin Section -->
                @if ($user->isAdmin())
                    @php
                        $adminManagementActive = request()->routeIs(
                            'admin.users.*',
                            'admin.courses.*',
                            'admin.exams.*',
                            'admin.questions.*',
                            'admin.question-bank.*',
                            'admin.forum-categories.*',
                            'admin.authorization-logs.*',
                            'admin.cheating-incidents.*',
                        );
                        $adminReportsActive = request()->routeIs('admin.analytics.*');
                        $adminSettingsActive = request()->routeIs(
                            'admin.certificate-settings.*',
                            'admin.ai-settings.*',
                        );
                        $adminPlatformActive = request()->routeIs(
                            'admin.schools.*',
                            'admin.settings.*',
                            'admin.documentation.*',
                        );
                    @endphp

                    <div class="space-y-1">
                        <p class="px-6 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 font-display">
                            {{ __('Administrasi') }}
                        </p>

                        <!-- Management Group -->
                        <x-sidebar-group :label="__('Manajemen')" icon="fas fa-tasks" :active="$adminManagementActive">
                            <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-users text-sm"></i>
                                </div>
                                <span>{{ __('Pengguna') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.courses.*')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-book text-sm"></i>
                                </div>
                                <span>{{ __('Kursus') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.exams.index')" :active="request()->routeIs('admin.exams.*') || request()->routeIs('admin.questions.*')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-clipboard-list text-sm"></i>
                                </div>
                                <span>{{ __('Ujian') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.question-bank.index')" :active="request()->routeIs('admin.question-bank.*')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-database text-sm"></i>
                                </div>
                                <span>{{ __('Bank Soal') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.forum-categories.index')" :active="request()->routeIs('admin.forum-categories.*')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-tags text-sm"></i>
                                </div>
                                <span>{{ __('Kategori Forum') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.authorization-logs.index')" :active="request()->routeIs('admin.authorization-logs.*')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-sm"></i>
                                </div>
                                <span>{{ __('Log Akses') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.cheating-incidents.index')" :active="request()->routeIs('admin.cheating-incidents.*')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-user-slash text-sm"></i>
                                </div>
                                <span>{{ __('Pelanggaran') }}</span>
                            </x-sidebar-link>
                        </x-sidebar-group>

                        <!-- Reports Group -->
                        <x-sidebar-group :label="__('Laporan')" icon="fas fa-chart-bar" :active="$adminReportsActive">
                            <x-sidebar-link :href="route('admin.analytics.index')" :active="request()->routeIs('admin.analytics.*')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-chart-area text-sm"></i>
                                </div>
                                <span>{{ __('Analitik') }}</span>
                            </x-sidebar-link>
                        </x-sidebar-group>

                        <!-- Platform Group -->
                        <x-sidebar-group :label="__('Platform')" icon="fas fa-server" :active="$adminPlatformActive">
                            <x-sidebar-link :href="route('admin.schools.index')" :active="request()->routeIs('admin.schools.*')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-school text-sm"></i>
                                </div>
                                <span>{{ __('Sekolah') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-sliders-h text-sm"></i>
                                </div>
                                <span>{{ __('Pengaturan Umum') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.certificate-settings.index')" :active="request()->routeIs('admin.certificate-settings.*')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-certificate text-sm"></i>
                                </div>
                                <span>{{ __('Sertifikat') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.ai-settings.index')" :active="request()->routeIs('admin.ai-settings.*')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-robot text-sm"></i>
                                </div>
                                <span>{{ __('Pengaturan AI') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('admin.documentation.index')" :active="request()->routeIs('admin.documentation.*')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-book-reader text-sm"></i>
                                </div>
                                <span>{{ __('Dokumentasi') }}</span>
                            </x-sidebar-link>
                        </x-sidebar-group>
                    </div>
                @elseif($user->isGuru())
                    @php
                        $guruContentActive = request()->routeIs('guru.courses.*', 'guru.exams.*', 'guru.questions.*');
                        $guruAnalyticsActive = request()->routeIs('guru.analytics.*');
                        $guruReportsActive = request()->routeIs('guru.reports.*');
                    @endphp

                    <div class="space-y-1">
                        <p class="px-6 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 font-display">
                            {{ __('Area Pengajar') }}
                        </p>

                        <x-sidebar-group :label="__('Konten Saya')" icon="fas fa-chalkboard-teacher" :active="$guruContentActive">
                            <x-sidebar-link :href="route('guru.courses.index')" :active="request()->routeIs('guru.courses.*')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-book-open text-sm"></i>
                                </div>
                                <span>{{ __('Kursus Saya') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('guru.exams.index')" :active="request()->routeIs('guru.exams.*') || request()->routeIs('guru.questions.*')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-file-alt text-sm"></i>
                                </div>
                                <span>{{ __('Ujian Saya') }}</span>
                            </x-sidebar-link>
                        </x-sidebar-group>

                        <x-sidebar-link :href="route('guru.analytics.index')" :active="$guruAnalyticsActive">
                            <div class="w-5 h-5 flex items-center justify-center">
                                <i class="fas fa-chart-line text-sm"></i>
                            </div>
                            <span>{{ __('Analitik Siswa') }}</span>
                        </x-sidebar-link>

                        <x-sidebar-link :href="route('guru.reports.index')" :active="$guruReportsActive">
                            <div class="w-5 h-5 flex items-center justify-center">
                                <i class="fas fa-file-contract text-sm"></i>
                            </div>
                            <span>{{ __('Laporan Nilai') }}</span>
                        </x-sidebar-link>
                    </div>
                @elseif($user->isSiswa())
                    @php
                        $studentCoursesActive = request()->routeIs('siswa.courses.*');
                        $studentExamsActive = request()->routeIs('siswa.exams.*', 'siswa.reports.*');
                        $studentAnalyticsActive = request()->routeIs('siswa.analytics.*');
                    @endphp

                    <div class="space-y-1">
                        <p class="px-6 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 font-display">
                            {{ __('Pembelajaran') }}
                        </p>

                        <x-sidebar-group :label="__('Kursus')" icon="fas fa-graduation-cap" :active="$studentCoursesActive">
                            <x-sidebar-link :href="route('siswa.courses.index')" :active="request()->routeIs('siswa.courses.index')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-search text-sm"></i>
                                </div>
                                <span>{{ __('Jelajahi Kursus') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('siswa.courses.my-courses')" :active="request()->routeIs('siswa.courses.my-courses')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-book-reader text-sm"></i>
                                </div>
                                <span>{{ __('Kursus Saya') }}</span>
                            </x-sidebar-link>
                        </x-sidebar-group>

                        <x-sidebar-group :label="__('Ujian & Nilai')" icon="fas fa-pen-alt" :active="$studentExamsActive">
                            <x-sidebar-link :href="route('siswa.exams.index')" :active="request()->routeIs('siswa.exams.*')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-edit text-sm"></i>
                                </div>
                                <span>{{ __('Daftar Ujian') }}</span>
                            </x-sidebar-link>

                            <x-sidebar-link :href="route('siswa.reports.index')" :active="request()->routeIs('siswa.reports.*')">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="fas fa-certificate text-sm"></i>
                                </div>
                                <span>{{ __('Transkrip Nilai') }}</span>
                            </x-sidebar-link>
                        </x-sidebar-group>

                        <x-sidebar-link :href="route('siswa.analytics.index')" :active="$studentAnalyticsActive">
                            <div class="w-5 h-5 flex items-center justify-center">
                                <i class="fas fa-chart-pie text-sm"></i>
                            </div>
                            <span>{{ __('Progres Belajar') }}</span>
                        </x-sidebar-link>
                    </div>
                @endif
            </nav>
        @endif

        <!-- Footer Info -->
        <div class="mt-auto px-6 py-4 text-[10px] text-gray-400 text-center">
            <p>&copy; {{ date('Y') }} Koneksi LMS</p>
            <p>v1.0.0</p>
        </div>
    </div>
</div>
