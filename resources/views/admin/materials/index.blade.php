<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Materials - :title', ['title' => $course->title]) }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ __('Kode: :code', ['code' => $course->code]) }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.courses.materials.create', $course) }}"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-2"></i>{{ __('Add Material') }}
                </a>
                <a href="{{ route('admin.courses.show', $course) }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Materials Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Order') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Material') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Tipe') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Pembuat') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Status') }}</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($materials as $material)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            #{{ $material->order }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <i
                                                    class="{{ $material->getFileIcon() }} {{ $material->getFileColorClass() }} text-2xl mr-3"></i>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $material->title }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        @if ($material->type === 'file' && $material->file_name)
                                                            {{ $material->file_name }}
                                                            ({{ $material->getFormattedFileSize() }})
                                                        @elseif($material->url)
                                                            {{ Str::limit($material->url, 50) }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $material->type_display }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $material->creator->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $material->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $material->is_published ? __('Published') : __('Draft') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.courses.materials.show', [$course, $material]) }}"
                                                class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.courses.materials.edit', [$course, $material]) }}"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form
                                                action="{{ route('admin.courses.materials.destroy', [$course, $material]) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirmDelete('{{ __('Are you sure you want to delete this material?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
                                            <p class="text-lg">{{ __('No materials yet') }}</p>
                                            <a href="{{ route('admin.courses.materials.create', $course) }}"
                                                class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                                {{ __('Add your first material') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($materials->hasPages())
                        <div class="mt-4">
                            {{ $materials->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
