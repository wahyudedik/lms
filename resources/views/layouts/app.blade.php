<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- PWA Meta Tags -->
    <meta name="description" content="Complete Learning Management System with CBT, Materials, and Reports">
    <meta name="theme-color" content="#6D28D9">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="LMS">
    <meta name="mobile-web-app-capable" content="yes">

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/icons/icon-192x192.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white border-b border-gray-200 shadow-sm">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- Global SweetAlert Scripts -->
    <script>
        // Global SweetAlert configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Show success messages
        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif

        // Show error messages
        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            });
        @endif

        // Show info messages
        @if (session('info'))
            Toast.fire({
                icon: 'info',
                title: '{{ session('info') }}'
            });
        @endif

        // Show warning messages
        @if (session('warning'))
            Toast.fire({
                icon: 'warning',
                title: '{{ session('warning') }}'
            });
        @endif

        // Enhanced confirm dialogs - Returns a Promise for programmatic use
        function confirmDelete(message = 'Are you sure you want to delete this item?') {
            return Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            });
        }

        // Handle all delete confirmations globally for forms with onsubmit
        document.addEventListener('DOMContentLoaded', function() {
            // Remove onsubmit attribute from all forms that use confirmDelete
            // and handle via event listener instead
            document.querySelectorAll('form[onsubmit*="confirmDelete"]').forEach(form => {
                // Extract message from onsubmit attribute before removing it
                const onsubmitAttr = form.getAttribute('onsubmit');
                const messageMatch = onsubmitAttr.match(/confirmDelete\(['"](.+?)['"]\)/);
                const message = messageMatch ? messageMatch[1] :
                    'Are you sure you want to delete this item?';

                // Store message in data attribute
                form.dataset.confirmMessage = message;

                // Remove onsubmit to prevent conflicts
                form.removeAttribute('onsubmit');

                // Add event listener
                form.addEventListener('submit', async function(e) {
                    // Check if this is a confirmed submission
                    if (form.dataset.confirmed === 'true') {
                        // Allow submission
                        delete form.dataset.confirmed;
                        return true;
                    }

                    e.preventDefault(); // Prevent default submission
                    e.stopPropagation();

                    // Show SweetAlert with stored message
                    const result = await Swal.fire({
                        title: 'Are you sure?',
                        text: form.dataset.confirmMessage ||
                            'Are you sure you want to delete this item?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    });

                    // If confirmed, mark as confirmed and submit
                    if (result.isConfirmed) {
                        form.dataset.confirmed = 'true';
                        form.submit();
                    }
                });
            });
        });

        // Enhanced toggle status confirmation
        function confirmToggleStatus(isActive, userName) {
            const action = isActive ? 'deactivate' : 'activate';
            return Swal.fire({
                title: `Are you sure?`,
                text: `Do you want to ${action} ${userName}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: isActive ? '#d33' : '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Yes, ${action}!`,
                cancelButtonText: 'Cancel'
            });
        }
    </script>

    <!-- AI Chat Widget -->
    <x-ai-chat-widget />

    @stack('scripts')

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful:', registration.scope);
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed:', err);
                    });
            });
        }

        // PWA Install Prompt
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent the mini-infobar from appearing on mobile
            e.preventDefault();
            // Stash the event so it can be triggered later
            deferredPrompt = e;
            // Show install button (optional - can be added to UI later)
            console.log('PWA install prompt available');
        });

        // Track PWA installation
        window.addEventListener('appinstalled', () => {
            console.log('PWA installed successfully');
            deferredPrompt = null;
        });
    </script>
</body>

</html>
