<section>
    <header class="border-b border-gray-200 pb-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-purple-100 rounded-lg">
                <i class="fas fa-camera text-purple-600"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Foto Profil</h2>
                <p class="text-sm text-gray-600">Perbarui foto profil Anda. Maksimal 2MB. Format: JPG, PNG, GIF</p>
            </div>
        </div>
    </header>

    <div class="mt-6">
        <!-- Current Photo Display -->
        <div class="flex items-center gap-4 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex-shrink-0">
                <img class="h-20 w-20 rounded-full object-cover border-2 border-purple-200"
                    src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" id="current-photo">
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-900">{{ auth()->user()->name }}</h3>
                <p class="text-sm text-gray-600">
                    {{ auth()->user()->role_display }}
                </p>
            </div>
        </div>

        <!-- Upload Form -->
        <form id="photo-upload-form" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-image text-gray-400 mr-1"></i>
                        Pilih Foto
                    </label>
                    <input id="profile_photo" name="profile_photo" type="file"
                        class="block w-full text-sm text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 file:cursor-pointer border border-gray-300 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-purple-500"
                        accept="image/*" required />
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle"></i>
                        Format yang didukung: JPG, PNG, GIF. Maksimal ukuran file: 2MB
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" id="upload-btn"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white rounded-lg shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-upload"></i>
                        <span>Upload Foto</span>
                    </button>

                    @if (auth()->user()->profile_photo)
                        <button type="button" id="delete-btn"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-trash-alt"></i>
                            <span>Hapus Foto</span>
                        </button>
                    @endif
                </div>
            </div>
        </form>

        <!-- Progress Bar -->
        <div id="progress-container" class="hidden mt-4">
            <div class="bg-gray-200 rounded-full h-2.5 overflow-hidden">
                <div id="progress-bar" class="bg-purple-600 h-2.5 rounded-full transition-all duration-300"
                    style="width: 0%"></div>
            </div>
            <p id="progress-text" class="text-sm text-gray-600 mt-2 flex items-center gap-2">
                <i class="fas fa-spinner fa-spin text-purple-600"></i>
                <span>Mengupload...</span>
            </p>
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
        const currentPhoto = document.getElementById('current-photo');
        const sidebarPhoto = document.getElementById('sidebar-photo');

        // Upload form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const fileInput = document.getElementById('profile_photo');

            if (!fileInput.files[0]) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Silakan pilih file untuk diupload.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                return;
            }

            // Show progress
            progressContainer.classList.remove('hidden');
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Mengupload...</span>';

            // Create XMLHttpRequest for progress tracking
            const xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    progressBar.style.width = percentComplete + '%';
                    progressText.innerHTML =
                        `<i class="fas fa-spinner fa-spin text-purple-600"></i><span>Mengupload... ${Math.round(percentComplete)}%</span>`;
                }
            });

            xhr.addEventListener('load', function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        const newPhotoUrl = response.photo_url + '?t=' + new Date().getTime();
                        currentPhoto.src = newPhotoUrl;
                        if (sidebarPhoto) sidebarPhoto.src = newPhotoUrl;
                        form.reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Upload gagal.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000
                        });
                    }
                } else {
                    const response = JSON.parse(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Upload gagal.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000
                    });
                }

                progressContainer.classList.add('hidden');
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '<i class="fas fa-upload"></i><span>Upload Foto</span>';
            });

            xhr.addEventListener('error', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan jaringan.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000
                });
                progressContainer.classList.add('hidden');
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '<i class="fas fa-upload"></i><span>Upload Foto</span>';
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
                    title: 'Hapus Foto Profil?',
                    text: 'Apakah Anda yakin ingin menghapus foto profil Anda?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Ya, Hapus',
                    cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal'
                });

                if (result.isConfirmed) {
                    deleteBtn.disabled = true;
                    deleteBtn.innerHTML =
                        '<i class="fas fa-spinner fa-spin"></i><span>Menghapus...</span>';

                    fetch('{{ route('profile.photo.delete') }}', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message,
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                });
                                const newPhotoUrl =
                                    '{{ auth()->user()->profile_photo_url }}?t=' + new Date()
                                    .getTime();
                                currentPhoto.src = newPhotoUrl;
                                if (sidebarPhoto) sidebarPhoto.src = newPhotoUrl;
                                deleteBtn.style.display = 'none';
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: data.message || 'Hapus gagal.',
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 4000
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan jaringan.',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 4000
                            });
                        })
                        .finally(() => {
                            deleteBtn.disabled = false;
                            deleteBtn.innerHTML =
                                '<i class="fas fa-trash-alt"></i><span>Hapus Foto</span>';
                        });
                }
            });
        }
    });
</script>
