<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Photo') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Update your profile photo. Maximum file size: 2MB. Allowed formats: JPG, PNG, GIF.') }}
        </p>
    </header>

    <div class="mt-6">
        <!-- Current Photo Display -->
        <div class="flex items-center space-x-4 mb-6">
            <div class="flex-shrink-0">
                <img class="h-20 w-20 rounded-full object-cover" src="{{ auth()->user()->profile_photo_url }}"
                    alt="{{ auth()->user()->name }}'s profile photo" id="current-photo">
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900">{{ auth()->user()->name }}</h3>
                <p class="text-sm text-gray-500">{{ auth()->user()->role_display }}</p>
            </div>
        </div>

        <!-- Upload Form -->
        <form id="photo-upload-form" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <x-input-label for="profile_photo" :value="__('Choose Photo')" />
                    <input id="profile_photo" name="profile_photo" type="file"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        accept="image/*" required />
                    <x-input-error :messages="$errors->get('profile_photo')" class="mt-2" />
                </div>

                <div class="flex items-center space-x-4">
                    <x-primary-button type="submit" id="upload-btn">
                        {{ __('Upload Photo') }}
                    </x-primary-button>

                    @if (auth()->user()->profile_photo)
                        <x-danger-button type="button" id="delete-btn">
                            {{ __('Delete Photo') }}
                        </x-danger-button>
                    @endif
                </div>
            </div>
        </form>

        <!-- Progress Bar -->
        <div id="progress-container" class="hidden mt-4">
            <div class="bg-gray-200 rounded-full h-2">
                <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                    style="width: 0%"></div>
            </div>
            <p id="progress-text" class="text-sm text-gray-600 mt-2">Uploading...</p>
        </div>

        <!-- Success/Error Messages -->
        <div id="message-container" class="hidden mt-4">
            <div id="success-message"
                class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <span id="success-text"></span>
            </div>
            <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <span id="error-text"></span>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('photo-upload-form');
        const uploadBtn = document.getElementById('upload-btn');
        const deleteBtn = document.getElementById('delete-btn');
        const progressContainer = document.getElementById('progress-container');
        const progressBar = document.getElementById('progress-bar');
        const progressText = document.getElementById('progress-text');
        const messageContainer = document.getElementById('message-container');
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');
        const successText = document.getElementById('success-text');
        const errorText = document.getElementById('error-text');
        const currentPhoto = document.getElementById('current-photo');

        // Upload form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const fileInput = document.getElementById('profile_photo');

            if (!fileInput.files[0]) {
                showError('Please select a file to upload.');
                return;
            }

            // Show progress
            showProgress();
            uploadBtn.disabled = true;
            uploadBtn.textContent = 'Uploading...';

            // Create XMLHttpRequest for progress tracking
            const xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    updateProgress(percentComplete);
                }
            });

            xhr.addEventListener('load', function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        showSuccess(response.message);
                        currentPhoto.src = response.photo_url + '?t=' + new Date().getTime();
                        form.reset();
                    } else {
                        showError(response.message || 'Upload failed.');
                    }
                } else {
                    const response = JSON.parse(xhr.responseText);
                    showError(response.message || 'Upload failed.');
                }

                hideProgress();
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload Photo';
            });

            xhr.addEventListener('error', function() {
                showError('Network error occurred.');
                hideProgress();
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload Photo';
            });

            xhr.open('POST', '{{ route('profile.photo.upload') }}');
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]')
                .getAttribute('content'));
            xhr.send(formData);
        });

        // Delete photo
        if (deleteBtn) {
            deleteBtn.addEventListener('click', async function() {
                const result = await Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to delete your profile photo?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                });

                if (result.isConfirmed) {
                    deleteBtn.disabled = true;
                    deleteBtn.textContent = 'Deleting...';

                    fetch('{{ route('profile.photo.delete') }}', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'Content-Type': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showSuccess(data.message);
                                currentPhoto.src =
                                    '{{ auth()->user()->profile_photo_url }}?t=' +
                                    new Date().getTime();
                                deleteBtn.style.display = 'none';
                            } else {
                                showError(data.message || 'Delete failed.');
                            }
                        })
                        .catch(error => {
                            showError('Network error occurred.');
                        })
                        .finally(() => {
                            deleteBtn.disabled = false;
                            deleteBtn.textContent = 'Delete Photo';
                        });
                }
            });
        }

        function showProgress() {
            progressContainer.classList.remove('hidden');
            messageContainer.classList.add('hidden');
        }

        function hideProgress() {
            progressContainer.classList.add('hidden');
        }

        function updateProgress(percent) {
            progressBar.style.width = percent + '%';
            progressText.textContent = `Uploading... ${Math.round(percent)}%`;
        }

        function showSuccess(message) {
            messageContainer.classList.remove('hidden');
            successMessage.classList.remove('hidden');
            errorMessage.classList.add('hidden');
            successText.textContent = message;
        }

        function showError(message) {
            messageContainer.classList.remove('hidden');
            errorMessage.classList.remove('hidden');
            successMessage.classList.add('hidden');
            errorText.textContent = message;
        }
    });
</script>
