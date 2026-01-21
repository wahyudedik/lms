<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __(config('app.name', 'Laravel')) }}</title>

    <!-- PWA Meta Tags -->
    <meta name="description" content="{{ __('Complete Learning Management System with CBT, Materials, and Reports') }}">
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
    @php
        $authUser = Auth::user();
    @endphp
    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-gray-50">
        <div class="flex min-h-screen">
            @if ($authUser)
                @include('layouts.navigation')
            @endif

            <div class="flex-1 flex flex-col min-h-screen">
                <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 py-4">
                        <div class="flex items-center gap-3">
                            @if ($authUser)
                                <button class="lg:hidden rounded-lg border border-gray-200 p-2 text-gray-600 hover:bg-gray-50"
                                    @click="sidebarOpen = true">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                            @endif

                            <div>
                                <p class="text-xs uppercase tracking-wider text-gray-500">
                                    {{ $authUser ? __('Welcome back,') : __('Guest Access') }}</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $authUser ? $authUser->name : __('Guest User') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @if ($authUser)
                                <x-notification-bell />

                                <x-dropdown align="right" width="56">
                                    <x-slot name="trigger">
                                        <button
                                            class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-150 shadow-sm">
                                            <img class="h-9 w-9 rounded-full object-cover ring-2 ring-gray-100"
                                                src="{{ $authUser->profile_photo_url }}" alt="{{ $authUser->name }}">
                                            <div class="text-left hidden sm:block">
                                                <div class="text-sm font-semibold text-gray-900">{{ $authUser->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $authUser->role_display }}</div>
                                            </div>
                                            <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('profile.edit')">
                                            <i class="fas fa-user mr-2"></i>{{ __('Profile') }}
                                        </x-dropdown-link>

                                        @if ($authUser->isAdmin())
                                            <x-dropdown-link :href="route('admin.schools.index')">
                                                <i class="fas fa-school mr-2"></i>{{ __('Schools') }}
                                            </x-dropdown-link>

                                            <x-dropdown-link :href="route('admin.settings.index')">
                                                <i class="fas fa-cog mr-2"></i>{{ __('Settings') }}
                                            </x-dropdown-link>

                                            <x-dropdown-link :href="route('admin.documentation.index')">
                                                <i class="fas fa-book mr-2"></i>{{ __('Documentation') }}
                                            </x-dropdown-link>
                                        @endif

                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf

                                            <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault();
                                                                this.closest('form').submit();">
                                                <i class="fas fa-sign-out-alt mr-2"></i>{{ __('Log Out') }}
                                            </x-dropdown-link>
                                        </form>
                                    </x-slot>
                                </x-dropdown>
                            @else
                                <a href="{{ route('login') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-sign-in-alt"></i>
                                    {{ __('Log In') }}
                                </a>
                            @endif
                        </div>
                    </div>

                    @isset($header)
                        <div class="border-t border-gray-100 px-4 sm:px-6 lg:px-8 py-3">
                            {{ $header }}
                        </div>
                    @endisset
                </header>

                <main class="flex-1">
                    <div class="page-shell">
                        <div class="page-section">
                        @isset($slot)
                            {{ $slot }}
                        @else
                            @yield('content')
                        @endisset
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Global SweetAlert Scripts -->
    <script>
        const localeTexts = {
            confirmTitle: @json(__('Are you sure?')),
            confirmDefaultMessage: @json(__('Are you sure you want to delete this item?')),
            confirmYesDelete: @json(__('Yes, delete it!')),
            confirmCancel: @json(__('Cancel')),
            toggleActionActivate: @json(__('activate')),
            toggleActionDeactivate: @json(__('deactivate')),
            togglePromptTemplate: @json(__('Do you want to :action :name?', ['action' => ':action', 'name' => ':name'])),
            toggleConfirmTemplate: @json(__('Yes, :action!')),
            resetTitle: @json(__('Reset Login Access?')),
            resetTextTemplate: @json(__("This will restore :name's ability to log in.", ['name' => ':name'])),
            resetConfirm: @json(__('Yes, reset it!'))
        };

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
        function confirmDelete(message = localeTexts.confirmDefaultMessage) {
            return Swal.fire({
                title: localeTexts.confirmTitle,
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: localeTexts.confirmYesDelete,
                cancelButtonText: localeTexts.confirmCancel
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
                const message = messageMatch ? messageMatch[1] : localeTexts.confirmDefaultMessage;

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
                        title: localeTexts.confirmTitle,
                        text: form.dataset.confirmMessage || localeTexts.confirmDefaultMessage,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: localeTexts.confirmYesDelete,
                        cancelButtonText: localeTexts.confirmCancel
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
            const actionText = isActive ? localeTexts.toggleActionDeactivate : localeTexts.toggleActionActivate;
            const promptText = localeTexts.togglePromptTemplate
                .replace(':action', actionText)
                .replace(':name', userName);
            const confirmText = localeTexts.toggleConfirmTemplate.replace(':action', actionText);
            return Swal.fire({
                title: localeTexts.confirmTitle,
                text: promptText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: isActive ? '#d33' : '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmText,
                cancelButtonText: localeTexts.confirmCancel
            });
        }

        function confirmResetLogin(userName) {
            const resetText = localeTexts.resetTextTemplate.replace(':name', userName);
            return Swal.fire({
                title: localeTexts.resetTitle,
                text: resetText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#6b7280',
                confirmButtonText: localeTexts.resetConfirm,
                cancelButtonText: localeTexts.confirmCancel
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
