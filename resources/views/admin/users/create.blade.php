<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-user-plus mr-2"></i>{{ __('Create New User') }}
            </h2>
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-arrow-left"></i>
                {{ __('Back to Users') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user text-gray-400 mr-1"></i>{{ __('Full Name') }}
                                </label>
                                <x-text-input id="name" class="block w-full" type="text" name="name"
                                    :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-envelope text-gray-400 mr-1"></i>{{ __('Email') }}
                                </label>
                                <x-text-input id="email" class="block w-full" type="email" name="email"
                                    :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Role -->
                            <div>
                                <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user-tag text-gray-400 mr-1"></i>{{ __('Role') }}
                                </label>
                                <select id="role" name="role"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150"
                                    required>
                                    <option value="">{{ __('Select Role') }}</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                        {{ __('Admin') }}</option>
                                    <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>
                                        {{ __('Guru') }}</option>
                                    <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>
                                        {{ __('Siswa') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>

                            <!-- Class -->
                            <div>
                                <label for="school_class_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-school text-gray-400 mr-1"></i>{{ __('Class') }}
                                </label>
                                <select id="school_class_id" name="school_class_id"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                    <option value="">{{ __('Select Class') }}</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}"
                                            {{ (string) old('school_class_id') === (string) $class->id ? 'selected' : '' }}>
                                            {{ $class->name }} ({{ $class->education_level_label }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('school_class_id')" class="mt-2" />
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-phone text-gray-400 mr-1"></i>{{ __('Phone Number') }}
                                </label>
                                <x-text-input id="phone" class="block w-full" type="tel" name="phone"
                                    :value="old('phone')" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>

                            <!-- Birth Date -->
                            <div>
                                <label for="birth_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-calendar text-gray-400 mr-1"></i>{{ __('Birth Date') }}
                                </label>
                                <x-text-input id="birth_date" class="block w-full" type="date" name="birth_date"
                                    :value="old('birth_date')" />
                                <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                            </div>

                            <!-- Gender -->
                            <div>
                                <label for="gender" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-venus-mars text-gray-400 mr-1"></i>{{ __('Gender') }}
                                </label>
                                <select id="gender" name="gender"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                    <option value="">{{ __('Select Gender') }}</option>
                                    <option value="laki-laki" {{ old('gender') == 'laki-laki' ? 'selected' : '' }}>
                                        {{ __('Male') }}</option>
                                    <option value="perempuan" {{ old('gender') == 'perempuan' ? 'selected' : '' }}>
                                        {{ __('Female') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-lock text-gray-400 mr-1"></i>{{ __('Password') }}
                                </label>
                                <x-text-input id="password" class="block w-full" type="password" name="password"
                                    required />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-lock text-gray-400 mr-1"></i>{{ __('Confirm Password') }}
                                </label>
                                <x-text-input id="password_confirmation" class="block w-full" type="password"
                                    name="password_confirmation" required />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mt-6">
                            <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>{{ __('Address') }}
                            </label>
                            <textarea id="address" name="address" rows="3"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150"
                                placeholder="{{ __('Enter full address') }}">{{ old('address') }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div class="mt-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', true) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm font-semibold text-gray-700">{{ __('Active User') }}</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.users.index') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                <i class="fas fa-times"></i>
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" id="createUserBtn"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-save"></i>
                                {{ __('Create User') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('createUserBtn').addEventListener('click', function(e) {
                const form = this.closest('form');
                const formData = new FormData(form);
                const createUserLocale = {
                    missingTitle: @json(__('Missing Required Fields')),
                    missingText: @json(__('Please fill in all required fields before creating the user.')),
                    loadingTitle: @json(__('Creating User...')),
                    loadingText: @json(__('Please wait while we create the new user.')),
                };

                // Check if required fields are filled
                const name = formData.get('name');
                const email = formData.get('email');
                const role = formData.get('role');
                const password = formData.get('password');

                if (!name || !email || !role || !password) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: createUserLocale.missingTitle,
                        text: createUserLocale.missingText,
                    });
                    return;
                }

                // Show loading
                Swal.fire({
                    title: createUserLocale.loadingTitle,
                    text: createUserLocale.loadingText,
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
