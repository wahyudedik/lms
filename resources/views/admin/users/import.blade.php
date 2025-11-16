<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Import Users') }}
            </h2>
            <a href="{{ route('admin.users.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                {{ __('Back to Users') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Import Users from Excel/CSV') }}</h3>

                        <!-- Instructions -->
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">{{ __('📋 Import Instructions:') }}</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• {{ __('Download the template file first to see the correct format') }}</li>
                                <li>• <strong>{{ __('Required fields:') }}</strong> {{ __('name, email') }}</li>
                                <li>• <strong>{{ __('Optional fields:') }}</strong> {{ __('role, phone, birth_date, gender, address, status') }}
                                </li>
                                <li>• <strong>{{ __('Supported roles:') }}</strong> {{ __('admin, guru, siswa') }}</li>
                                <li>• <strong>{{ __('Supported genders:') }}</strong> {{ __('laki-laki, perempuan') }}</li>
                                <li>• <strong>{{ __('Supported status:') }}</strong> {{ __('active, inactive') }}</li>
                                <li>• <strong>{{ __('Date format:') }}</strong> {{ __('YYYY-MM-DD (e.g., 2000-01-01)') }}</li>
                                <li>• <strong>{{ __('Maximum file size:') }}</strong> 10MB</li>
                            </ul>
                        </div>

                        <!-- Password Info -->
                        <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
                            <h4 class="text-sm font-medium text-green-800 mb-2">{{ __('🔐 Default Password:') }}</h4>
                            <div class="bg-white border-2 border-green-300 rounded-md p-3 mb-3">
                                <p class="text-center">
                                    <span class="text-xs text-gray-600">{{ __('All users will use password:') }}</span><br>
                                    <code
                                        class="text-lg font-bold text-green-700 bg-green-100 px-3 py-1 rounded">LMS2024@Pass</code>
                                </p>
                            </div>
                            <ul class="text-sm text-green-700 space-y-1">
                                <li>• <strong>{{ __('No need to fill password during import') }}</strong></li>
                                <li>• {{ __('System automatically sets password :password for all users', ['password' => 'LMS2024@Pass']) }}</li>
                                <li>• {{ __('After import, export the Excel file to get user + password list') }}</li>
                                <li>• {{ __('Share the export file to WhatsApp/Telegram groups') }}</li>
                                <li>• {{ __('Users log in with their email + password :password', ['password' => 'LMS2024@Pass']) }}</li>
                                <li>• <strong>{{ __('Remind users to change password immediately after login') }}</strong></li>
                            </ul>
                        </div>

                        <!-- Download Template Button -->
                        <div class="mb-6">
                            <a href="{{ route('admin.users.template') }}"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                {{ __('Download Template') }}
                            </a>
                        </div>
                    </div>

                    <!-- Import Form -->
                    <form action="{{ route('admin.users.import.store') }}" method="POST" enctype="multipart/form-data"
                        id="importForm">
                        @csrf

                        <div class="mb-6">
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Select Excel/CSV File') }}
                            </label>
                            <input type="file" id="file" name="file" accept=".xlsx,.xls,.csv"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                required>
                            @error('file')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('admin.users.index') }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                id="importBtn">
                                {{ __('Import Users') }}
                            </button>
                        </div>
                    </form>

                    <!-- Progress Bar (hidden by default) -->
                    <div id="progressContainer" class="mt-6 hidden">
                        <div class="bg-gray-200 rounded-full h-2.5">
                            <div id="progressBar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                                style="width: 0%"></div>
                        </div>
                        <p id="progressText" class="text-sm text-gray-600 mt-2">{{ __('Preparing import...') }}</p>
                    </div>
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
