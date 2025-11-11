<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Photo Section -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-photo-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Profile Update Success
        @if (session('status') === 'profile-updated')
            Swal.fire({
                icon: 'success',
                title: 'Profile Updated!',
                text: 'Your profile information has been saved successfully.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        // Password Update Success
        @if (session('status') === 'password-updated')
            Swal.fire({
                icon: 'success',
                title: 'Password Updated!',
                text: 'Your password has been changed successfully.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        // Email Verification Link Sent
        @if (session('status') === 'verification-link-sent')
            Swal.fire({
                icon: 'success',
                title: 'Verification Link Sent!',
                text: 'A new verification link has been sent to your email address.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        @endif

        // Show account deletion errors (only for critical errors)
        @if ($errors->userDeletion->any())
            @php
                $deletionErrors = [];
                foreach ($errors->userDeletion->all() as $error) {
                    $deletionErrors[] = $error;
                }
                $deletionErrorText = implode('<br>', $deletionErrors);
            @endphp
            Swal.fire({
                icon: 'error',
                title: 'Account Deletion Failed!',
                html: '{!! $deletionErrorText !!}',
                confirmButtonText: 'OK'
            });
        @endif
    </script>
    @endpush
</x-app-layout>
