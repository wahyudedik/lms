<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-poll text-purple-600 mr-2"></i>My Learning Analytics
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm">Enrolled Courses</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_courses']) }}</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-book-open text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm">Exams Taken</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_exams_taken']) }}</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-clipboard-list text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">Average Score</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['avg_score'], 1) }}%</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-award text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>Exam Results
                        </h3>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-green-600">{{ $stats['exams_passed'] }}</p>
                            <p class="text-xs text-gray-500">Passed</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Pass Rate</span>
                                <span
                                    class="font-semibold">{{ $stats['total_exams_taken'] > 0 ? number_format(($stats['exams_passed'] / $stats['total_exams_taken']) * 100, 1) : 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-green-600 h-3 rounded-full transition-all duration-300"
                                    style="width: {{ $stats['total_exams_taken'] > 0 ? ($stats['exams_passed'] / $stats['total_exams_taken']) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-chart-line text-blue-600 mr-2"></i>Average Progress
                        </h3>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['avg_progress'], 1) }}%
                            </p>
                            <p class="text-xs text-gray-500">Completion</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Course Progress</span>
                                <span class="font-semibold">{{ number_format($stats['avg_progress'], 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-blue-600 h-3 rounded-full transition-all duration-300"
                                    style="width: {{ $stats['avg_progress'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Trend -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-line text-purple-600 mr-2"></i>My Performance Trend (Last 20 Exams)
                </h3>
                <div class="h-80">
                    <canvas id="performanceTrendChart"></canvas>
                </div>
            </div>

            <!-- Two Column Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Performance by Course -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-book text-blue-600 mr-2"></i>Performance by Course
                    </h3>
                    <div class="h-80">
                        <canvas id="performanceByCourseChart"></canvas>
                    </div>
                </div>

                <!-- Pass/Fail Ratio -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-chart-pie text-green-600 mr-2"></i>Exam Results Overview
                    </h3>
                    <div class="h-80">
                        <canvas id="passFailChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Exam Scores -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-history text-orange-600 mr-2"></i>Recent Exam Scores (Last 10)
                </h3>
                <div class="h-80">
                    <canvas id="recentScoresChart"></canvas>
                </div>
            </div>

            <!-- Study Time Distribution -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-calendar-week text-indigo-600 mr-2"></i>My Study Activity by Day
                </h3>
                <div class="h-80">
                    <canvas id="studyTimeChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            let charts = {};

            async function initializeCharts() {
                // Performance Trend
                const trendData = await fetchData('{{ route('siswa.analytics.performance-trend') }}');
                charts.performanceTrend = new Chart(document.getElementById('performanceTrendChart'), {
                    type: 'line',
                    data: trendData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + '%';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Score (%)'
                                }
                            }
                        }
                    }
                });

                // Performance by Course
                const courseData = await fetchData('{{ route('siswa.analytics.performance-by-course') }}');
                charts.performanceByCourse = new Chart(document.getElementById('performanceByCourseChart'), {
                    type: 'polarArea',
                    data: courseData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        },
                        scales: {
                            r: {
                                beginAtZero: true,
                                max: 100
                            }
                        }
                    }
                });

                // Pass/Fail Ratio
                const passFailData = await fetchData('{{ route('siswa.analytics.exam-pass-fail-ratio') }}');
                charts.passFail = new Chart(document.getElementById('passFailChart'), {
                    type: 'doughnut',
                    data: passFailData,
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

                // Recent Scores
                const scoresData = await fetchData('{{ route('siswa.analytics.recent-exam-scores') }}');
                charts.recentScores = new Chart(document.getElementById('recentScoresChart'), {
                    type: 'bar',
                    data: scoresData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100
                            }
                        }
                    }
                });

                // Study Time Distribution
                const studyData = await fetchData('{{ route('siswa.analytics.study-time-distribution') }}');
                charts.studyTime = new Chart(document.getElementById('studyTimeChart'), {
                    type: 'bar',
                    data: studyData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                },
                                title: {
                                    display: true,
                                    text: 'Number of Activities'
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
