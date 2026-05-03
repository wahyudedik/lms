<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-certificate mr-2"></i>{{ __('Certificate Details') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('certificates.download', $certificate->certificate_number) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-download"></i>
                    <span>{{ __('Download PDF') }}</span>
                </a>
                <a href="{{ route('certificates.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span>{{ __('Back to Certificates') }}</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-green-600 text-xl mr-3 mt-0.5"></i>
                        <div>
                            <h4 class="font-semibold text-green-900 mb-1">{{ __('Success!') }}</h4>
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Certificate Info Card -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>{{ __('Certificate Information') }}
                    </h3>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Certificate Number -->
                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <dt class="text-xs font-semibold text-blue-700 mb-1">
                                <i class="fas fa-hashtag mr-1"></i>{{ __('Certificate Number') }}
                            </dt>
                            <dd class="text-lg font-bold text-blue-900">{{ $certificate->certificate_number }}</dd>
                        </div>

                        <!-- Issue Date -->
                        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                            <dt class="text-xs font-semibold text-green-700 mb-1">
                                <i class="fas fa-calendar mr-1"></i>{{ __('Issue Date') }}
                            </dt>
                            <dd class="text-lg font-bold text-green-900">
                                {{ $certificate->issue_date->format('F d, Y') }}
                            </dd>
                        </div>

                        <!-- Student Name -->
                        <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                            <dt class="text-xs font-semibold text-purple-700 mb-1">
                                <i class="fas fa-user mr-1"></i>{{ __('Student Name') }}
                            </dt>
                            <dd class="text-lg font-bold text-purple-900">{{ $certificate->student_name }}</dd>
                        </div>

                        <!-- Course Title -->
                        <div class="p-4 bg-orange-50 rounded-lg border border-orange-200">
                            <dt class="text-xs font-semibold text-orange-700 mb-1">
                                <i class="fas fa-book mr-1"></i>{{ __('Course') }}
                            </dt>
                            <dd class="text-lg font-bold text-orange-900">{{ $certificate->course_title }}</dd>
                        </div>

                        <!-- Grade -->
                        @if ($certificate->grade)
                            <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <dt class="text-xs font-semibold text-yellow-700 mb-1">
                                    <i class="fas fa-star mr-1"></i>{{ __('Grade') }}
                                </dt>
                                <dd class="text-lg font-bold text-yellow-900">{{ $certificate->grade }}</dd>
                            </div>
                        @endif

                        <!-- Final Score -->
                        @if ($certificate->final_score)
                            <div class="p-4 bg-teal-50 rounded-lg border border-teal-200">
                                <dt class="text-xs font-semibold text-teal-700 mb-1">
                                    <i class="fas fa-chart-line mr-1"></i>{{ __('Final Score') }}
                                </dt>
                                <dd class="text-lg font-bold text-teal-900">{{ $certificate->final_score }}%</dd>
                            </div>
                        @endif
                    </div>

                    <!-- Course Description -->
                    @if ($certificate->course_description)
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <dt class="text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-align-left text-gray-400 mr-1"></i>{{ __('Course Description') }}
                            </dt>
                            <dd class="text-sm text-gray-600">{{ $certificate->course_description }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics Card -->
            @if ($certificate->metadata)
                <div class="bg-white overflow-hidden shadow-md rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-chart-bar text-green-600 mr-2"></i>{{ __('Achievement Statistics') }}
                        </h3>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            @if (isset($certificate->metadata['total_hours']))
                                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-blue-600">
                                    <div class="flex items-center">
                                        <div class="p-3 bg-blue-100 rounded-lg mr-3">
                                            <i class="fas fa-clock text-blue-600 text-2xl"></i>
                                        </div>
                                        <div>
                                            <div class="text-blue-600 text-xs font-semibold mb-1">{{ __('Total Hours') }}</div>
                                            <div class="text-2xl font-bold text-blue-900">{{ $certificate->metadata['total_hours'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (isset($certificate->metadata['completed_lessons']))
                                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-green-600">
                                    <div class="flex items-center">
                                        <div class="p-3 bg-green-100 rounded-lg mr-3">
                                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                                        </div>
                                        <div>
                                            <div class="text-green-600 text-xs font-semibold mb-1">{{ __('Lessons') }}</div>
                                            <div class="text-2xl font-bold text-green-900">{{ $certificate->metadata['completed_lessons'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (isset($certificate->metadata['exams_passed']))
                                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-purple-600">
                                    <div class="flex items-center">
                                        <div class="p-3 bg-purple-100 rounded-lg mr-3">
                                            <i class="fas fa-file-alt text-purple-600 text-2xl"></i>
                                        </div>
                                        <div>
                                            <div class="text-purple-600 text-xs font-semibold mb-1">{{ __('Exams Passed') }}</div>
                                            <div class="text-2xl font-bold text-purple-900">{{ $certificate->metadata['exams_passed'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (isset($certificate->metadata['completion_rate']))
                                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-orange-600">
                                    <div class="flex items-center">
                                        <div class="p-3 bg-orange-100 rounded-lg mr-3">
                                            <i class="fas fa-percentage text-orange-600 text-2xl"></i>
                                        </div>
                                        <div>
                                            <div class="text-orange-600 text-xs font-semibold mb-1">{{ __('Completion') }}</div>
                                            <div class="text-2xl font-bold text-orange-900">{{ $certificate->metadata['completion_rate'] }}%</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Verification Card -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-shield-alt text-teal-600 mr-2"></i>{{ __('Verification') }}
                    </h3>
                </div>

                <div class="p-6">
                    <div class="bg-teal-50 border border-teal-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-teal-600 text-2xl mr-3 mt-0.5"></i>
                            <div class="flex-1">
                                <h4 class="font-semibold text-teal-900 mb-2">{{ __('This certificate is verified and authentic') }}</h4>
                                <p class="text-sm text-teal-700 mb-3">
                                    {{ __('Anyone can verify this certificate using the certificate number or QR code.') }}
                                </p>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('certificates.verify', $certificate->certificate_number) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-teal-600 text-white font-semibold rounded-lg hover:bg-teal-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                        <i class="fas fa-search"></i>
                                        <span>{{ __('Verify Certificate') }}</span>
                                    </a>
                                    <button onclick="copyVerificationUrl()"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-teal-300 text-teal-700 font-semibold rounded-lg hover:bg-teal-50 hover:border-teal-400 transition-all duration-200 shadow-sm">
                                        <i class="fas fa-copy"></i>
                                        <span>{{ __('Copy Verification URL') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Certificate Preview -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-eye text-purple-600 mr-2"></i>{{ __('Certificate Preview') }}
                    </h3>
                </div>

                <div class="p-6">
                    <div class="bg-gray-100 rounded-lg p-8 text-center">
                        <i class="fas fa-file-pdf text-gray-400 text-6xl mb-4"></i>
                        <p class="text-gray-600 mb-4">{{ __('Click the button below to download your certificate as PDF') }}</p>
                        <a href="{{ route('certificates.download', $certificate->certificate_number) }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-download"></i>
                            <span>{{ __('Download Certificate PDF') }}</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            function copyVerificationUrl() {
                const url = '{{ route('certificates.verify', $certificate->certificate_number) }}';
                navigator.clipboard.writeText(url).then(() => {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('Copied!') }}',
                        text: '{{ __('Verification URL copied to clipboard') }}',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }).catch(err => {
                    console.error('Failed to copy:', err);
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('Error') }}',
                        text: '{{ __('Failed to copy URL') }}'
                    });
                });
            }
        </script>
    @endpush
</x-app-layout>
