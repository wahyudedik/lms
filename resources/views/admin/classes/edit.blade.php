<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Class') }}
            </h2>
            <a href="{{ route('admin.classes.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.classes.update', $class) }}" method="POST" x-data="{ isGeneral: @js(old('is_general', $class->is_general) ? true : false) }">
                        @csrf
                        @method('PATCH')

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Class Name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $class->name) }}"
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="education_level" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Education Level') }}
                                </label>
                                <select name="education_level" id="education_level" :disabled="isGeneral"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('education_level') border-red-500 @enderror">
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
                                <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Student Capacity') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="capacity" id="capacity"
                                    value="{{ old('capacity', $class->capacity) }}" min="1" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('capacity') border-red-500 @enderror">
                                @error('capacity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_general" value="1" x-model="isGeneral"
                                    {{ $generalClassExists ? 'disabled' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 disabled:opacity-50">
                                <span class="ml-2 text-sm text-gray-700">{{ __('General Class') }}</span>
                            </label>
                            @error('is_general')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @if ($generalClassExists)
                                <p class="mt-1 text-xs text-gray-500">{{ __('General class already exists.') }}</p>
                            @endif
                            <p class="mt-1 text-xs text-gray-500">
                                {{ __('Use general class for students without a specific class.') }}</p>
                        </div>

                        <div class="flex gap-4">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-save mr-2"></i>{{ __('Save') }}
                            </button>
                            <a href="{{ route('admin.classes.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
