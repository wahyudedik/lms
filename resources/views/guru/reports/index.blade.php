@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-chart-bar text-blue-600 mr-2"></i>Laporan Nilai & Ujian
            </h1>
            <p class="text-gray-600">Kelola dan export laporan nilai ujian siswa</p>
        </div>

        <!-- Course Filter -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" action="{{ route('guru.reports.index') }}" class="flex gap-4 items-end">
                <div class="flex-1">
                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-book mr-1"></i>Pilih Kursus
                    </label>
                    <select name="course_id" id="course_id"
                        class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required>
                        <option value="">-- Pilih Kursus --</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }} ({{ $course->exams_count }} Ujian)
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </form>
        </div>

        @if ($exams->count() > 0)
            <!-- Exams List with Export Options -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-clipboard-list text-green-600 mr-2"></i>Daftar Ujian & Export
                    </h2>
                </div>
                <div class="divide-y">
                    @foreach ($exams as $exam)
                        <div class="p-6 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between">
                                <!-- Exam Info -->
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">
                                        {{ $exam->title }}
                                        <span
                                            class="ml-2 text-xs px-2 py-1 rounded-full {{ $exam->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($exam->status) }}
                                        </span>
                                    </h3>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <p><i
                                                class="fas fa-question-circle text-blue-500 mr-2"></i><strong>{{ $exam->questions_count ?? 0 }}</strong>
                                            Soal</p>
                                        <p><i
                                                class="fas fa-users text-purple-500 mr-2"></i><strong>{{ $exam->attempts_count ?? 0 }}</strong>
                                            Siswa Mengerjakan</p>
                                        <p><i
                                                class="fas fa-check-circle text-green-500 mr-2"></i><strong>{{ $exam->graded_attempts_count ?? 0 }}</strong>
                                            Sudah Dinilai</p>
                                    </div>
                                </div>

                                <!-- Export Actions -->
                                <div class="ml-6 space-y-2">
                                    <a href="{{ route('guru.reports.export-grades-excel', $exam) }}"
                                        class="flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                                    </a>
                                    <a href="{{ route('guru.reports.export-grades-pdf', $exam) }}"
                                        class="flex items-center px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
                                        <i class="fas fa-file-pdf mr-2"></i>Export PDF
                                    </a>
                                    <a href="{{ route('guru.exams.results', $exam) }}"
                                        class="flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                                        <i class="fas fa-chart-line mr-2"></i>Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Student Transcript Generator (Optional) -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-user-graduate text-indigo-600 mr-2"></i>Export Transkrip Siswa
                </h3>
                <p class="text-gray-600 text-sm mb-4">Export transkrip lengkap untuk satu siswa di kursus ini.</p>

                <form
                    action="{{ route('guru.reports.student-transcript-pdf', ['course' => request('course_id'), 'student' => '__STUDENT_ID__']) }}"
                    method="GET" class="flex gap-4 items-end" id="transcriptForm">
                    <div class="flex-1">
                        <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-1"></i>Pilih Siswa
                        </label>
                        <select name="student_id" id="student_id"
                            class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            required>
                            <option value="">-- Pilih Siswa --</option>
                            @php
                                $course = $courses->firstWhere('id', request('course_id'));
                                if ($course) {
                                    $enrollments = $course
                                        ->enrollments()
                                        ->with('user:id,name,email')
                                        ->where('status', 'active')
                                        ->get();
                                    foreach ($enrollments as $enrollment) {
                                        echo '<option value="' .
                                            $enrollment->user_id .
                                            '">' .
                                            $enrollment->user->name .
                                            ' (' .
                                            $enrollment->user->email .
                                            ')</option>';
                                    }
                                }
                            @endphp
                        </select>
                    </div>
                    <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-file-pdf mr-2"></i>Export Transkrip PDF
                    </button>
                </form>
            </div>
        @elseif(request('course_id'))
            <!-- No Exams Found -->
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Ujian</h3>
                <p class="text-gray-500">Kursus ini belum memiliki ujian. Buat ujian terlebih dahulu.</p>
                <a href="{{ route('guru.exams.create') }}"
                    class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>Buat Ujian Baru
                </a>
            </div>
        @else
            <!-- Select Course Prompt -->
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Pilih Kursus</h3>
                <p class="text-gray-500">Silakan pilih kursus di atas untuk melihat laporan nilai</p>
            </div>
        @endif
    </div>

    <script>
        // Update form action dynamically when student is selected
        document.getElementById('student_id')?.addEventListener('change', function() {
            const form = document.getElementById('transcriptForm');
            const courseId = document.querySelector('[name="course_id"]').value;
            const studentId = this.value;

            if (courseId && studentId) {
                form.action = form.action.replace('__STUDENT_ID__', studentId);
                form.action = form.action.replace('__COURSE_ID__', courseId);
            }
        });
    </script>
@endsection
