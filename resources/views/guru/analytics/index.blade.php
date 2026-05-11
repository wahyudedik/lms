<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-chart-area mr-2"></i>My Teaching Analytics
            </h2>
            <select id="course_filter"
                class="px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150 text-sm"
                onchange="refreshCourseCharts()">
                <option value="">All Courses</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-green-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg mr-3">
                            <i class="fas fa-book text-green-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-green-600 text-xs font-semibold mb-1">My Courses</div>
                            <div class="text-2xl font-bold text-green-900">{{ number_format($stats['total_courses']) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-blue-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg mr-3">
                            <i class="fas fa-users text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-blue-600 text-xs font-semibold mb-1">Total Students</div>
                            <div class="text-2xl font-bold text-blue-900">{{ number_format($stats['total_students']) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-purple-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg mr-3">
                            <i class="fas fa-clipboard-check text-purple-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-purple-600 text-xs font-semibold mb-1">Exam Attempts</div>
                            <div class="text-2xl font-bold text-purple-900">
                                {{ number_format($stats['total_attempts']) }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-orange-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 rounded-lg mr-3">
                            <i class="fas fa-star text-orange-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-orange-600 text-xs font-semibold mb-1">Avg Exam Score</div>
                            <div class="text-2xl font-bold text-orange-900">{{ number_format($stats['avg_score'], 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignment Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-indigo-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-indigo-100 rounded-lg mr-3">
                            <i class="fas fa-tasks text-indigo-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-indigo-600 text-xs font-semibold mb-1">Total Tugas</div>
                            <div class="text-2xl font-bold text-indigo-900">
                                {{ number_format($stats['total_assignments']) }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-teal-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-teal-100 rounded-lg mr-3">
                            <i class="fas fa-file-upload text-teal-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-teal-600 text-xs font-semibold mb-1">Total Pengumpulan</div>
                            <div class="text-2xl font-bold text-teal-900">
                                {{ number_format($stats['total_submissions']) }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-rose-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-rose-100 rounded-lg mr-3">
                            <i class="fas fa-chart-line text-rose-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-rose-600 text-xs font-semibold mb-1">Rata-rata Nilai Tugas</div>
                            <div class="text-2xl font-bold text-rose-900">
                                {{ number_format($stats['avg_assignment_score'], 1) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Performance by Course -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-bar text-blue-600 mr-2"></i>Student Performance by Course
                </h3>
                <div class="h-96">
                    <canvas id="studentPerformanceChart"></canvas>
                </div>
            </div>

            <!-- Two Column Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Exam Completion Rate -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-tasks text-green-600 mr-2"></i>Exam Completion Rate
                    </h3>
                    <div class="h-80">
                        <canvas id="examCompletionChart"></canvas>
                    </div>
                </div>

                <!-- Grade Distribution -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-graduation-cap text-purple-600 mr-2"></i>Grade Distribution
                    </h3>
                    <div class="mb-3">
                        <select id="exam_filter" class="w-full rounded-md border-gray-300 shadow-sm text-sm"
                            onchange="refreshGradeDistribution()">
                            <option value="">Select Exam</option>
                            @foreach ($courses->flatMap->exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->course->title }} - {{ $exam->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="h-64">
                        <canvas id="gradeDistributionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Assignment Score by Course -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-bar text-indigo-600 mr-2"></i>Rata-rata Nilai Tugas vs Ujian per Kursus
                </h3>
                <div class="h-80">
                    <canvas id="assignmentScoreChart"></canvas>
                </div>
            </div>

            <!-- Assignment Completion Rate -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-check-circle text-teal-600 mr-2"></i>Tingkat Penyelesaian Tugas per Siswa
                </h3>
                <div class="mb-3">
                    <select id="completion_course_filter" class="rounded-md border-gray-300 shadow-sm text-sm"
                        onchange="refreshAssignmentCompletion()">
                        <option value="">Pilih Kursus</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="h-80">
                    <canvas id="assignmentCompletionChart"></canvas>
                </div>
            </div>

            <!-- Student Engagement -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-line text-indigo-600 mr-2"></i>Student Engagement (Progress %)
                </h3>
                <div class="mb-3">
                    <select id="engagement_course_filter" class="rounded-md border-gray-300 shadow-sm text-sm"
                        onchange="refreshEngagement()">
                        <option value="">Select Course</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="h-80">
                    <canvas id="studentEngagementChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            let charts = {};

            async function initializeCharts() {
                // Student Performance
                const performanceData = await fetchData(
                    '{{ route(auth()->user()->getRolePrefix() . '.analytics.student-performance-by-course') }}');
                charts.studentPerformance = new Chart(document.getElementById('studentPerformanceChart'), {
                    type: 'bar',
                    data: performanceData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Jumlah Siswa'
                                }
                            },
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Rata-rata Nilai (%)'
                                },
                                grid: {
                                    drawOnChartArea: false,
                                }
                            }
                        }
                    }
                });

                // Exam Completion
                const completionData = await fetchData(
                    '{{ route(auth()->user()->getRolePrefix() . '.analytics.exam-completion-rate') }}');
                charts.examCompletion = new Chart(document.getElementById('examCompletionChart'), {
                    type: 'bar',
                    data: completionData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            x: {
                                stacked: true,
                            },
                            y: {
                                stacked: true,
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Assignment Score by Course
                const assignmentScoreData = await fetchData(
                    '{{ route(auth()->user()->getRolePrefix() . '.analytics.assignment-score-by-course') }}');
                charts.assignmentScore = new Chart(document.getElementById('assignmentScoreChart'), {
                    type: 'bar',
                    data: assignmentScoreData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Rata-rata Nilai'
                                }
                            }
                        }
                    }
                });
            }

            async function refreshGradeDistribution() {
                const examId = document.getElementById('exam_filter').value;
                if (!examId) return;

                if (charts.gradeDistribution) charts.gradeDistribution.destroy();

                const data = await fetchData(
                    '{{ route(auth()->user()->getRolePrefix() . '.analytics.grade-distribution') }}?exam_id=' + examId);
                charts.gradeDistribution = new Chart(document.getElementById('gradeDistributionChart'), {
                    type: 'pie',
                    data: data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }
                });
            }

            async function refreshEngagement() {
                const courseId = document.getElementById('engagement_course_filter').value;
                if (!courseId) return;

                if (charts.studentEngagement) charts.studentEngagement.destroy();

                const data = await fetchData(
                    '{{ route(auth()->user()->getRolePrefix() . '.analytics.student-engagement-metrics') }}?course_id=' +
                    courseId);
                charts.studentEngagement = new Chart(document.getElementById('studentEngagementChart'), {
                    type: 'radar',
                    data: data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                beginAtZero: true,
                                max: 100
                            }
                        }
                    }
                });
            }

            async function refreshAssignmentCompletion() {
                const courseId = document.getElementById('completion_course_filter').value;
                if (!courseId) return;

                if (charts.assignmentCompletion) charts.assignmentCompletion.destroy();

                const data = await fetchData(
                    '{{ route(auth()->user()->getRolePrefix() . '.analytics.assignment-completion-rate') }}?course_id=' +
                    courseId);
                charts.assignmentCompletion = new Chart(document.getElementById('assignmentCompletionChart'), {
                    type: 'bar',
                    data: data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Penyelesaian (%)'
                                }
                            }
                        }
                    }
                });
            }

            async function refreshCourseCharts() {
                const courseId = document.getElementById('course_filter').value;

                if (charts.studentPerformance) charts.studentPerformance.destroy();

                const performanceData = await fetchData(
                    '{{ route(auth()->user()->getRolePrefix() . '.analytics.student-performance-by-course') }}?course_id=' +
                    courseId);
                charts.studentPerformance = new Chart(document.getElementById('studentPerformanceChart'), {
                    type: 'bar',
                    data: performanceData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                position: 'left',
                            },
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                max: 100,
                                grid: {
                                    drawOnChartArea: false,
                                }
                            }
                        }
                    }
                });
            }

            async function fetchData(url) {
                const response = await fetch(url);
                return await response.json();
            }

            document.addEventListener('DOMContentLoaded', initializeCharts);
        </script>
    @endpush
</x-app-layout>
