@props(['name' => 'icon', 'value' => 'fas fa-info-circle'])

<div x-data="{
    selectedIcon: '{{ old($name, $value) }}',
    showPicker: false,
    icons: [
        { class: 'fas fa-info-circle', label: 'Info' },
        { class: 'fas fa-exclamation-triangle', label: 'Warning' },
        { class: 'fas fa-check-circle', label: 'Success' },
        { class: 'fas fa-times-circle', label: 'Error' },
        { class: 'fas fa-bell', label: 'Bell' },
        { class: 'fas fa-bullhorn', label: 'Announcement' },
        { class: 'fas fa-calendar-alt', label: 'Calendar' },
        { class: 'fas fa-clock', label: 'Clock' },
        { class: 'fas fa-book', label: 'Book' },
        { class: 'fas fa-graduation-cap', label: 'Education' },
        { class: 'fas fa-clipboard-list', label: 'Exam' },
        { class: 'fas fa-file-alt', label: 'Document' },
        { class: 'fas fa-star', label: 'Star' },
        { class: 'fas fa-trophy', label: 'Trophy' },
        { class: 'fas fa-heart', label: 'Heart' },
        { class: 'fas fa-lightbulb', label: 'Idea' },
        { class: 'fas fa-flag', label: 'Flag' },
        { class: 'fas fa-gift', label: 'Gift' },
        { class: 'fas fa-megaphone', label: 'Megaphone' },
        { class: 'fas fa-users', label: 'Users' },
        { class: 'fas fa-user-graduate', label: 'Student' },
        { class: 'fas fa-chalkboard-teacher', label: 'Teacher' },
        { class: 'fas fa-laptop', label: 'Laptop' },
        { class: 'fas fa-shield-alt', label: 'Shield' },
        { class: 'fas fa-lock', label: 'Lock' },
        { class: 'fas fa-map-marker-alt', label: 'Location' },
        { class: 'fas fa-envelope', label: 'Email' },
        { class: 'fas fa-thumbtack', label: 'Pin' },
        { class: 'fas fa-fire', label: 'Fire' },
        { class: 'fas fa-bolt', label: 'Lightning' },
    ]
}">
    <input type="hidden" :name="'{{ $name }}'" :value="selectedIcon">

    <!-- Selected Icon Display + Trigger -->
    <div class="relative">
        <button type="button" @click="showPicker = !showPicker"
            class="w-full flex items-center gap-3 px-4 py-2.5 border border-gray-300 rounded-lg hover:border-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition bg-white">
            <span class="w-8 h-8 flex items-center justify-center bg-gray-100 rounded-lg">
                <i :class="selectedIcon" class="text-lg text-gray-700"></i>
            </span>
            <span class="flex-1 text-left text-sm text-gray-700"
                x-text="icons.find(i => i.class === selectedIcon)?.label || 'Select Icon'"></span>
            <i class="fas fa-chevron-down text-xs text-gray-400"></i>
        </button>

        <!-- Dropdown Picker -->
        <div x-show="showPicker" x-transition @click.outside="showPicker = false"
            class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg p-3">
            <div class="grid grid-cols-6 gap-2">
                <template x-for="icon in icons" :key="icon.class">
                    <button type="button" @click="selectedIcon = icon.class; showPicker = false" :title="icon.label"
                        class="flex flex-col items-center justify-center p-2 rounded-lg border transition hover:bg-blue-50 hover:border-blue-300"
                        :class="selectedIcon === icon.class ? 'bg-blue-100 border-blue-400' : 'border-gray-200'">
                        <i :class="icon.class" class="text-lg mb-1"
                            :class="selectedIcon === icon.class ? 'text-blue-600' : 'text-gray-600'"></i>
                        <span class="text-[10px] text-gray-500 truncate w-full text-center" x-text="icon.label"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>
