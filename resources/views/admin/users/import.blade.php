<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-file-import mr-2"></i>{{ __('Import Users') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.users.template') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-download"></i>
                    {{ __('Download Template') }}
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Instructions Card -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6 shadow-md">
                <h3 class="text-lg font-bold text-blue-900 mb-4">
                    <i class="fas fa-info-circle text-blue-700 mr-2"></i>{{ __('Import Instructions') }}
                </h3>
                <ol class="list-decimal list-inside space-y-2 text-sm text-blue-800">
                    <li>{{ __('Download the Excel template using the button above') }}</li>
                    <li><strong>{{ __('Required fields:') }}</strong> {{ __('name, email') }}</li>
                    <li><strong>{{ __('Optional fields:') }}</strong> {{ __('role, phone, birth_date, gender, address, status') }}</li>
                    <li><strong>{{ __('Supported roles:') }}</strong> {{ __('admin, guru, siswa') }}</li>
                    <li><strong>{{ __('Supported genders:') }}</strong> {{ __('laki-laki, perempuan') }}</li>
                    <li><strong>{{ __('Date format:') }}</strong> {{ __('YYYY-MM-DD (e.g., 2000-01-01)') }}</li>
                    <li><strong>{{ __('Maximum file size:') }}</strong> 10MB</li>
                </ol>
            </div>

            <!-- Password Info Card -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6 shadow-md">
                <h3 class="text-lg font-bold text-green-900 mb-4">
                    <i class="fas fa-lock text-green-700 mr-2"></i>{{ __('Default Password') }}
                </h3>
                <div class="bg-white border-2 border-green-300 rounded-lg p-4 mb-4">
                    <p class="text-center">
                        <span class="text-sm text-gray-600 block mb-2">{{ __('All users will use password:') }}</span>
                        <code class="text-2xl font-bold text-green-700 bg-green-100 px-4 py-2 rounded-lg">LMS2024@Pass</code>
                    </p>
                </div>
                <ul class="text-sm text-green-800 space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-600 mr-2 mt-0.5"></i>
                        <span><strong>{{ __('No need to fill password during import') }}</strong></span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-600 mr-2 mt-0.5"></i>
                        <span>{{ __('System automatically sets password :password for all users', ['password' => 'LMS2024@Pass']) }}</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-600 mr-2 mt-0.5"></i>
                        <span>{{ __('After import, export the Excel file to get user + password list') }}</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2 mt-0.5"></i>
                        <span><strong>{{ __('Remind users to change password immediately after login') }}</strong></span>
                    </li>
                </ul>
            </div>

            <!-- Upload Form -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.users.import.store') }}" method="POST" enctype="multipart/form-data"
                        id="importForm">
                        @csrf

                        <!-- File Upload Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-upload text-purple-600 mr-2"></i>{{ __('Upload File') }}
                            </h3>

                            <div class="mb-6">
                                <label for="file" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-file-excel text-gray-400 mr-1"></i>{{ __('Excel/CSV File') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="file" id="file" name="file" accept=".xlsx,.xls,.csv" required
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-sm text-gray-500 mt-1">{{ __('Supported formats: XLSX, XLS, CSV (Max: 10MB)') }}</p>
                                @error('file')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.users.index') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                <i class="fas fa-times"></i>
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" id="importBtn"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-file-import"></i>
                                {{ __('Import Users') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('importForm').addEventListener('submit', function(e) {
                const fileInput = document.getElementById('file');
                const importBtn = document.getElementById('importBtn');
                const importLocale = {
                    noFileTitle: @json(__('No File Selected')),
                    noFileText: @json(__('Please select a file to import.')),
                    loadingTitle: @json(__('Importing Users...')),
                    loadingText: @json(__('Please wait while we process your file.')),
                    loadingButton: @json(__('Importing...')),
                };

                if (fileInput.files.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: importLocale.noFileTitle,
                        text: importLocale.noFileText,
                    });
                    return;
                }

                // Show loading dialog
                Swal.fire({
                    title: importLocale.loadingTitle,
                    text: importLocale.loadingText,
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Disable button
                importBtn.disabled = true;
                importBtn.textContent = importLocale.loadingButton;
            });
        </script>
    @endpush
</x-app-layout>
