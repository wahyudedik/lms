<div class="relative">
    <!-- Mobile backdrop -->
    <div class="fixed inset-0 z-40 bg-gray-900/40 lg:hidden" x-show="sidebarOpen" x-transition.opacity x-cloak
        @click="sidebarOpen = false"></div>

    <!-- Mobile drawer -->
    <div class="fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-2xl lg:hidden transform transition-transform duration-300 overflow-y-auto"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" x-cloak>
        @include('layouts.sidebar-menu', ['isMobile' => true])
    </div>

    <!-- Desktop sidebar -->
    <aside
        class="hidden lg:flex lg:flex-col lg:w-72 lg:shrink-0 lg:border-r lg:border-gray-100 bg-white/90 backdrop-blur lg:h-screen lg:sticky lg:top-0 lg:overflow-y-auto">
        @include('layouts.sidebar-menu', ['isMobile' => false])
    </aside>
</div>

