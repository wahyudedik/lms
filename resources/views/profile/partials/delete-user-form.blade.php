<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-danger-button id="delete-account-btn">{{ __('Delete Account') }}</x-danger-button>

    <form id="delete-account-form" method="post" action="{{ route('profile.destroy') }}" style="display: none;">
        @csrf
        @method('delete')
        <input type="password" name="password" id="delete-password-input" required />
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteBtn = document.getElementById('delete-account-btn');
            const deleteForm = document.getElementById('delete-account-form');
            const passwordInput = document.getElementById('delete-password-input');

            deleteBtn.addEventListener('click', async function() {
                const { value: password } = await Swal.fire({
                    title: 'Delete Account?',
                    html: `
                        <p class="text-left mb-4">Once your account is deleted, all of its resources and data will be permanently deleted. This action cannot be undone.</p>
                        <p class="text-left text-sm text-gray-600">Please enter your password to confirm:</p>
                        <input type="password" id="swal-password" class="swal2-input mt-3" placeholder="Enter your password" autocomplete="current-password">
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete my account',
                    cancelButtonText: 'Cancel',
                    focusConfirm: false,
                    preConfirm: () => {
                        const password = document.getElementById('swal-password').value;
                        if (!password) {
                            Swal.showValidationMessage('Please enter your password');
                            return false;
                        }
                        return password;
                    },
                    didOpen: () => {
                        document.getElementById('swal-password').focus();
                    }
                });

                if (password) {
                    passwordInput.value = password;
                    deleteForm.submit();
                }
            });
        });
    </script>
</section>
