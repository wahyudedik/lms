<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit User') }}
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

                    <!-- Success/Error Messages will be shown via SweetAlert -->

                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <x-input-label for="name" :value="__('Full Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                    :value="old('name', $user->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                    :value="old('email', $user->email)" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Role -->
                            <div>
                                <x-input-label for="role" :value="__('Role')" />
                                <select id="role" name="role"
                                    class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                    required>
                                    <option value="">{{ __('Select Role') }}</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                        {{ __('Admin') }}</option>
                                    <option value="guru" {{ old('role', $user->role) == 'guru' ? 'selected' : '' }}>
                                        {{ __('Guru') }}</option>
                                    <option value="siswa" {{ old('role', $user->role) == 'siswa' ? 'selected' : '' }}>
                                        {{ __('Siswa') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>

                            <!-- Phone -->
                            <div>
                                <x-input-label for="phone" :value="__('Phone Number')" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone"
                                    :value="old('phone', $user->phone)" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>

                            <!-- Birth Date -->
                            <div>
                                <x-input-label for="birth_date" :value="__('Birth Date')" />
                                <x-text-input id="birth_date" class="block mt-1 w-full" type="date" name="birth_date"
                                    :value="old('birth_date', $user->birth_date?->format('Y-m-d'))" />
                                <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                            </div>

                            <!-- Gender -->
                            <div>
                                <x-input-label for="gender" :value="__('Gender')" />
                                <select id="gender" name="gender"
                                    class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                    <option value="">{{ __('Select Gender') }}</option>
                                    <option value="laki-laki"
                                        {{ old('gender', $user->gender) == 'laki-laki' ? 'selected' : '' }}>{{ __('Male') }}
                                    </option>
                                    <option value="perempuan"
                                        {{ old('gender', $user->gender) == 'perempuan' ? 'selected' : '' }}>{{ __('Female') }}
                                    </option>
                                </select>
                                <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mt-6">
                            <x-input-label for="address" :value="__('Address')" />
                            <textarea id="address" name="address" rows="3"
                                class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                placeholder="{{ __('Enter full address') }}">{{ old('address', $user->address) }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div class="mt-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">{{ __('Active User') }}</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.users.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button id="updateUserBtn">
                                {{ __('Update User') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <!-- Password Update Section -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Update Password') }}</h3>
                        <form method="POST" action="{{ route('admin.users.update-password', $user) }}">
                            @csrf
                            @method('PATCH')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="new_password" :value="__('New Password')" />
                                    <x-text-input id="new_password" class="block mt-1 w-full" type="password"
                                        name="password" required />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="new_password_confirmation" :value="__('Confirm New Password')" />
                                    <x-text-input id="new_password_confirmation" class="block mt-1 w-full"
                                        type="password" name="password_confirmation" required />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <x-primary-button id="updatePasswordBtn">
                                    {{ __('Update Password') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Handle user update
            document.getElementById('updateUserBtn').addEventListener('click', function(e) {
                const form = this.closest('form');
                const formData = new FormData(form);
                const updateUserLocale = {
                    missingTitle: @json(__('Missing Required Fields')),
                    missingText: @json(__('Please fill in all required fields before updating the user.')),
                    loadingTitle: @json(__('Updating User...')),
                    loadingText: @json(__('Please wait while we update the user information.')),
                };

                // Check if required fields are filled
                const name = formData.get('name');
                const email = formData.get('email');
                const role = formData.get('role');

                if (!name || !email || !role) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: updateUserLocale.missingTitle,
                        text: updateUserLocale.missingText,
                    });
                    return;
                }

                // Show loading
                Swal.fire({
                    title: updateUserLocale.loadingTitle,
                    text: updateUserLocale.loadingText,
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
            });

            // Handle password update
            document.getElementById('updatePasswordBtn').addEventListener('click', function(e) {
                const form = this.closest('form');
                const formData = new FormData(form);
                const updatePasswordLocale = {
                    missingTitle: @json(__('Missing Password Fields')),
                    missingText: @json(__('Please fill in both password fields.')),
                    mismatchTitle: @json(__('Password Mismatch')),
                    mismatchText: @json(__('Password and confirmation do not match.')),
                    confirmTitle: @json(__('Update Password?')),
                    confirmText: @json(__('Are you sure you want to update the password for this user?')),
                    confirmYes: @json(__('Yes, update it!')),
                    confirmCancel: @json(__('Cancel')),
                    loadingTitle: @json(__('Updating Password...')),
                    loadingText: @json(__('Please wait while we update the password.')),
                };

                const password = formData.get('password');
                const passwordConfirmation = formData.get('password_confirmation');

                if (!password || !passwordConfirmation) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: updatePasswordLocale.missingTitle,
                        text: updatePasswordLocale.missingText,
                    });
                    return;
                }

                if (password !== passwordConfirmation) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: updatePasswordLocale.mismatchTitle,
                        text: updatePasswordLocale.mismatchText,
                    });
                    return;
                }

                // Show confirmation
                Swal.fire({
                    title: updatePasswordLocale.confirmTitle,
                    text: updatePasswordLocale.confirmText,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: updatePasswordLocale.confirmYes,
                    cancelButtonText: updatePasswordLocale.confirmCancel
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: updatePasswordLocale.loadingTitle,
                            text: updatePasswordLocale.loadingText,
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        form.submit();
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
