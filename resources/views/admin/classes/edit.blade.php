<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-edit mr-2"></i>{{ __('Edit Class') }}
            </h2>
            <a href="{{ route('admin.classes.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-arrow-left"></i>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.classes.update', $class) }}" method="POST" x-data="{ isGeneral: @js(old('is_general', $class->is_general) ? true : false) }">
                        @csrf
                        @method('PATCH')

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-tag text-gray-400 mr-1"></i>{{ __('Class Name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $class->name) }}"
                                required
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="education_level" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-graduation-cap text-gray-400 mr-1"></i>{{ __('Education Level') }}
                                </label>
                                <select name="education_level" id="education_level" :disabled="isGeneral"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150 @error('education_level') border-red-500 @enderror">
                                    <option value="">{{ __('Select Education Level') }}</option>
                                    @foreach ($educationLevels as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('education_level', $class->education_level) === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('education_level')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500" x-show="isGeneral">
                                    {{ __('General class does not require education level.') }}
                                </p>
                            </div>

                            <div>
                                <label for="capacity" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-users text-gray-400 mr-1"></i>{{ __('Student Capacity') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="capacity" id="capacity"
                                    value="{{ old('capacity', $class->capacity) }}" min="1" required
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150 @error('capacity') border-red-500 @enderror">
                                @error('capacity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_general" value="1" x-model="isGeneral"
                                    {{ $generalClassExists ? 'disabled' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 disabled:opacity-50">
                                <span class="ml-2 text-sm font-semibold text-gray-700">{{ __('General Class') }}</span>
                            </label>
                            @error('is_general')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @if ($generalClassExists)
                                <p class="mt-2 text-xs text-blue-700">
                                    <i class="fas fa-info-circle mr-1"></i>{{ __('General class already exists.') }}
                                </p>
                            @endif
                            <p class="mt-2 text-xs text-blue-700">
                                <i class="fas fa-lightbulb mr-1"></i>{{ __('Use general class for students without a specific class.') }}
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.classes.index') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                <i class="fas fa-times"></i>
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-save"></i>
                                {{ __('Save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
