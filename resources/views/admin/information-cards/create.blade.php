<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.information-cards.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-plus-circle mr-2"></i>{{ __('Create Information Card') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <form method="POST" action="{{ route('admin.information-cards.store') }}" id="cardForm"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title"
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }} <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                placeholder="{{ __('e.g., Pengumuman Ujian Semester') }}">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Content') }} <span
                                    class="text-red-500">*</span></label>
                            <x-quill-editor name="content" :value="old('content')" :placeholder="__('Write the information card content here...')" />
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Card Type & Icon -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="card_type"
                                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('Card Type') }} <span
                                        class="text-red-500">*</span></label>
                                <select name="card_type" id="card_type"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="info" {{ old('card_type') === 'info' ? 'selected' : '' }}>
                                        {{ __('Info (Blue)') }}</option>
                                    <option value="success" {{ old('card_type') === 'success' ? 'selected' : '' }}>
                                        {{ __('Success (Green)') }}</option>
                                    <option value="warning" {{ old('card_type') === 'warning' ? 'selected' : '' }}>
                                        {{ __('Warning (Yellow)') }}</option>
                                    <option value="danger" {{ old('card_type') === 'danger' ? 'selected' : '' }}>
                                        {{ __('Danger (Red)') }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Icon') }}</label>
                                <x-icon-picker name="icon" :value="old('icon', 'fas fa-info-circle')" />
                            </div>
                        </div>

                        <!-- Attachment & Video -->
                        <x-information-card-media-fields />

                        <!-- Target Roles -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Target Roles') }} <span
                                    class="text-red-500">*</span></label>
                            <div class="flex flex-wrap gap-3">
                                @foreach ($roles as $role)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="target_roles[]" value="{{ $role }}"
                                            {{ in_array($role, old('target_roles', [])) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ ucfirst($role) }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('target_roles')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Target Users (Optional) -->
                        <div x-data="{
                            targetMode: '{{ old('target_user_ids') ? 'specific' : 'all' }}',
                            searchQuery: ''
                        }">
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('Target Audience') }}</label>

                            <!-- Toggle: All vs Specific -->
                            <div class="flex gap-3 mb-3">
                                <label
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer transition"
                                    :class="targetMode === 'all' ? 'border-blue-500 bg-blue-50 text-blue-700' :
                                        'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" x-model="targetMode" value="all" class="hidden">
                                    <i class="fas fa-users text-sm"></i>
                                    <span class="text-sm font-medium">{{ __('All Users in Role') }}</span>
                                </label>
                                <label
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer transition"
                                    :class="targetMode === 'specific' ? 'border-blue-500 bg-blue-50 text-blue-700' :
                                        'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" x-model="targetMode" value="specific" class="hidden">
                                    <i class="fas fa-user-check text-sm"></i>
                                    <span class="text-sm font-medium">{{ __('Specific Users') }}</span>
                                </label>
                            </div>

                            <!-- Specific Users Selection -->
                            <div x-show="targetMode === 'specific'" x-transition
                                class="border border-gray-200 rounded-lg overflow-hidden">
                                <!-- Search -->
                                <div class="p-3 border-b border-gray-100 bg-gray-50">
                                    <div class="relative">
                                        <i
                                            class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                        <input type="text" x-model="searchQuery"
                                            placeholder="{{ __('Search by name or email...') }}"
                                            class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>

                                <!-- User List -->
                                <div class="max-h-60 overflow-y-auto p-2 space-y-1">
                                    @foreach ($users as $user)
                                        <label
                                            class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer transition"
                                            x-show="!searchQuery || '{{ strtolower($user->name . ' ' . $user->email) }}'.includes(searchQuery.toLowerCase())">
                                            <input type="checkbox" name="target_user_ids[]" value="{{ $user->id }}"
                                                {{ in_array($user->id, old('target_user_ids', [])) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    {{ $user->name }}</p>
                                                <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                            </div>
                                            <span
                                                class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-600 flex-shrink-0">{{ ucfirst($user->role) }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                @if ($users->isEmpty())
                                    <div class="p-4 text-center text-sm text-gray-500">
                                        {{ __('No users found') }}
                                    </div>
                                @endif
                            </div>

                            <p class="mt-2 text-xs text-gray-500" x-show="targetMode === 'all'">
                                <i
                                    class="fas fa-info-circle mr-1"></i>{{ __('Card will be shown to all users in the selected target roles.') }}
                            </p>
                        </div>

                        <!-- Schedule -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Schedule Type') }}
                                <span class="text-red-500">*</span></label>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="radio" name="schedule_type" value="always"
                                        {{ old('schedule_type', 'always') === 'always' ? 'checked' : '' }}
                                        class="text-blue-600 focus:ring-blue-500" onchange="toggleScheduleFields()">
                                    <span class="ml-2 text-sm text-gray-700">{{ __('Always (show forever)') }}</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="schedule_type" value="date_range"
                                        {{ old('schedule_type') === 'date_range' ? 'checked' : '' }}
                                        class="text-blue-600 focus:ring-blue-500" onchange="toggleScheduleFields()">
                                    <span
                                        class="ml-2 text-sm text-gray-700">{{ __('Date Range (show between specific dates)') }}</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="schedule_type" value="daily"
                                        {{ old('schedule_type') === 'daily' ? 'checked' : '' }}
                                        class="text-blue-600 focus:ring-blue-500" onchange="toggleScheduleFields()">
                                    <span class="ml-2 text-sm text-gray-700">{{ __('Daily (show every day)') }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Date Range Fields -->
                        <div id="dateRangeFields" class="grid grid-cols-1 md:grid-cols-2 gap-4"
                            style="display: none;">
                            <div>
                                <label for="start_date"
                                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('Start Date') }}</label>
                                <input type="date" name="start_date" id="start_date"
                                    value="{{ old('start_date') }}"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="end_date"
                                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('End Date') }}</label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- Sort Order & Active -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="sort_order"
                                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('Sort Order') }}</label>
                                <input type="number" name="sort_order" id="sort_order"
                                    value="{{ old('sort_order', 0) }}" min="0"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">{{ __('Lower number = shown first') }}</p>
                            </div>
                            <div class="flex items-center pt-6">
                                <label class="inline-flex items-center">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1"
                                        {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span
                                        class="ml-2 text-sm text-gray-700">{{ __('Active (visible to users)') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="mt-8 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.information-cards.index') }}"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-2"></i>{{ __('Create Card') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleScheduleFields() {
                const scheduleType = document.querySelector('input[name="schedule_type"]:checked')?.value;
                const dateRangeFields = document.getElementById('dateRangeFields');

                dateRangeFields.style.display = scheduleType === 'date_range' ? 'grid' : 'none';
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', toggleScheduleFields);
        </script>
    @endpush
</x-app-layout>
