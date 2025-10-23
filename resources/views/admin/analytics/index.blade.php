<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-chart-line text-blue-600 mr-2"></i>Advanced Analytics
            </h2>
            <div class="flex gap-2">
                <input type="date" id="start_date" value="{{ $startDate }}"
                    class="rounded-md border-gray-300 shadow-sm text-sm">
                <input type="date" id="end_date" value="{{ $endDate }}"
                    class="rounded-md border-gray-300 shadow-sm text-sm">
                <button onclick="refreshCharts()"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-sync-alt mr-1"></i>Refresh
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Export Buttons -->
            <div class="flex gap-2 justify-end">
                <button onclick="exportDashboardPDF('admin-analytics-{{ date(\'Y-m-d\') }}.pdf', 'Admin Analytics Dashboard')" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-lg">
                    <i class="fas fa-file-export mr-2"></i>Export Dashboard PDF
                </button>
            </div>

            <!-- Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm">Total Users</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_users']) }}</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">Active Courses</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['active_courses']) }}</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-book text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm">Total Exams</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_exams']) }}</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-clipboard-list text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm">Avg Score</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($stats['avg_exam_score'], 1) }}%</p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-chart-bar text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Registration Trend -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-user-plus text-blue-600 mr-2"></i>User Registration Trend
                </h3>
                <div class="h-80">
                    <canvas id="registrationTrendChart"></canvas>
                </div>
            </div>

            <!-- Two Column Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Course Enrollment Statistics -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-chart-pie text-green-600 mr-2"></i>Top 10 Popular Courses
                    </h3>
                    <div class="h-80">
                        <canvas id="courseEnrollmentChart"></canvas>
                    </div>
                </div>

                <!-- User Role Distribution -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-user-tag text-purple-600 mr-2"></i>User Role Distribution
                    </h3>
                    <div class="h-80">
                        <canvas id="roleDistributionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Exam Performance Statistics -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-award text-orange-600 mr-2"></i>Exam Performance Overview
                </h3>
                <div class="h-96">
                    <canvas id="examPerformanceChart"></canvas>
                </div>
            </div>

            <!-- Monthly Activity Statistics -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-calendar-alt text-indigo-600 mr-2"></i>Monthly Activity (Last 12 Months)
                </h3>
                <div class="h-80">
                    <canvas id="monthlyActivityChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        
        <!-- Chart Export Dependencies -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="{{ asset('js/chart-export.js') }}"></script>
        
        <script>
            let charts = {};

            // Initialize all charts
            async function initializeCharts() {
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;

                // User Registration Trend
                const registrationData = await fetchData(
                    '{{ route('admin.analytics.user-registration-trend') }}?start_date=' + startDate + '&end_date=' +
                    endDate);
                charts.registration = new Chart(document.getElementById('registrationTrendChart'), {
                    type: 'line',
                    data: registrationData,
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
                            title: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });

                // Course Enrollment
                const courseData = await fetchData('{{ route('admin.analytics.course-enrollment-stats') }}');
                charts.courseEnrollment = new Chart(document.getElementById('courseEnrollmentChart'), {
                    type: 'bar',
                    data: courseData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Role Distribution
                const roleData = await fetchData('{{ route('admin.analytics.user-role-distribution') }}');
                charts.roleDistribution = new Chart(document.getElementById('roleDistributionChart'), {
                    type: 'doughnut',
                    data: roleData,
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

                // Exam Performance
                const examData = await fetchData(
                    '{{ route('admin.analytics.exam-performance-stats') }}?start_date=' + startDate + '&end_date=' +
                    endDate);
                charts.examPerformance = new Chart(document.getElementById('examPerformanceChart'), {
                    type: 'bar',
                    data: examData,
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
                                max: 100
                            }
                        }
                    }
                });

                // Monthly Activity
                const activityData = await fetchData('{{ route('admin.analytics.monthly-activity-stats') }}');
                charts.monthlyActivity = new Chart(document.getElementById('monthlyActivityChart'), {
                    type: 'line',
                    data: activityData,
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
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            async function fetchData(url) {
                const response = await fetch(url);
                return await response.json();
            }

            function refreshCharts() {
                // Destroy existing charts
                Object.values(charts).forEach(chart => chart.destroy());
                charts = {};

                // Reinitialize
                initializeCharts();

                Toast.fire({
                    icon: 'success',
                    title: 'Charts refreshed successfully!'
                });
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', async function() {
                await initializeCharts();
                
                // Register all charts for export
                window.chartExporter.registerChart('registration', charts.registration);
                window.chartExporter.registerChart('courseEnrollment', charts.courseEnrollment);
                window.chartExporter.registerChart('roleDistribution', charts.roleDistribution);
                window.chartExporter.registerChart('examPerformance', charts.examPerformance);
                window.chartExporter.registerChart('monthlyActivity', charts.monthlyActivity);
            });
        </script>
    @endpush
</x-app-layout>
