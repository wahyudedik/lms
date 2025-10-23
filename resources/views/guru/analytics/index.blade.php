<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-chart-area text-green-600 mr-2"></i>My Teaching Analytics
            </h2>
            <select id="course_filter" class="rounded-md border-gray-300 shadow-sm text-sm"
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
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">My Courses</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_courses']) }}</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-book text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm">Total Students</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_students']) }}</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm">Exam Attempts</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_attempts']) }}</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-clipboard-check text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm">Avg Score</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['avg_score'], 1) }}%</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-star text-2xl"></i>
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
                const performanceData = await fetchData('{{ route('guru.analytics.student-performance-by-course') }}');
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
                const completionData = await fetchData('{{ route('guru.analytics.exam-completion-rate') }}');
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
            }

            async function refreshGradeDistribution() {
                const examId = document.getElementById('exam_filter').value;
                if (!examId) return;

                if (charts.gradeDistribution) charts.gradeDistribution.destroy();

                const data = await fetchData('{{ route('guru.analytics.grade-distribution') }}?exam_id=' + examId);
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

                const data = await fetchData('{{ route('guru.analytics.student-engagement-metrics') }}?course_id=' +
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

            async function refreshCourseCharts() {
                const courseId = document.getElementById('course_filter').value;

                if (charts.studentPerformance) charts.studentPerformance.destroy();

                const performanceData = await fetchData(
                    '{{ route('guru.analytics.student-performance-by-course') }}?course_id=' + courseId);
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
