<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-file-alt mr-2"></i>{{ __('Materials - :title', ['title' => $course->title]) }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Kode: {{ $course->code }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.materials.create', $course) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">{{ __('Add Material') }}</span>
                    <span class="sm:hidden">{{ __('Tambah') }}</span>
                </a>
                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.show', $course) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span class="hidden sm:inline">{{ __('Back') }}</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Materials -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-4 sm:p-6">
                    @if ($materials->count() > 0)
                        <!-- Desktop Table -->
                        <div class="hidden lg:block overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">#
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                                            Materi</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                                            Tipe</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                                            Status</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($materials as $material)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                {{ $material->order }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center">
                                                    <i
                                                        class="{{ $material->getFileIcon() }} {{ $material->getFileColorClass() }} text-xl mr-3"></i>
                                                    <div class="min-w-0">
                                                        <p
                                                            class="text-sm font-semibold text-gray-900 truncate max-w-xs">
                                                            {{ $material->title }}</p>
                                                        <p class="text-xs text-gray-500 truncate max-w-xs">
                                                            @if ($material->type === 'file' && $material->file_name)
                                                                {{ Str::limit($material->file_name, 30) }}
                                                                ({{ $material->getFormattedFileSize() }})
                                                            @elseif($material->url)
                                                                {{ Str::limit($material->url, 30) }}
                                                            @endif
                                                        </p>
                                                        <div class="mt-1 flex flex-wrap gap-1">
                                                            @if ($material->courseGroups && $material->courseGroups->count() > 0)
                                                                @foreach ($material->courseGroups as $group)
                                                                    <span
                                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-teal-100 text-teal-800"><i
                                                                            class="fas fa-users mr-1"></i>{{ $group->name }}</span>
                                                                @endforeach
                                                            @else
                                                                <span
                                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700"><i
                                                                        class="fas fa-globe mr-1"></i>Semua Siswa</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                    {{ $material->type_display }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $material->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    <i
                                                        class="fas fa-{{ $material->is_published ? 'check' : 'clock' }} mr-1"></i>
                                                    {{ $material->is_published ? 'Published' : 'Draft' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                                <div class="flex justify-end gap-2">
                                                    <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.materials.show', [$course, $material]) }}"
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100"
                                                        title="Lihat">
                                                        <i class="fas fa-eye text-sm"></i>
                                                    </a>
                                                    <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.materials.edit', [$course, $material]) }}"
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-100"
                                                        title="Edit">
                                                        <i class="fas fa-edit text-sm"></i>
                                                    </a>
                                                    <form
                                                        action="{{ route(auth()->user()->getRolePrefix() . '.courses.materials.destroy', [$course, $material]) }}"
                                                        method="POST" class="inline"
                                                        onsubmit="return confirmDelete('{{ __('Are you sure?') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100"
                                                            title="Hapus">
                                                            <i class="fas fa-trash text-sm"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Cards -->
                        <div class="lg:hidden space-y-3">
                            @foreach ($materials as $material)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start gap-3">
                                        <i
                                            class="{{ $material->getFileIcon() }} {{ $material->getFileColorClass() }} text-2xl mt-0.5"></i>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate">
                                                {{ $material->title }}</p>
                                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                    {{ $material->type_display }}
                                                </span>
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $material->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $material->is_published ? 'Published' : 'Draft' }}
                                                </span>
                                                <span class="text-xs text-gray-400">#{{ $material->order }}</span>
                                            </div>
                                            @if ($material->type === 'file' && $material->file_name)
                                                <p class="text-xs text-gray-500 mt-1 truncate">
                                                    {{ $material->file_name }}
                                                    ({{ $material->getFormattedFileSize() }})
                                                </p>
                                            @endif
                                            <div class="mt-1 flex flex-wrap gap-1">
                                                @if ($material->courseGroups && $material->courseGroups->count() > 0)
                                                    @foreach ($material->courseGroups as $group)
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-teal-100 text-teal-800"><i
                                                                class="fas fa-users mr-1"></i>{{ $group->name }}</span>
                                                    @endforeach
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700"><i
                                                            class="fas fa-globe mr-1"></i>Semua Siswa</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-end gap-2 mt-3 pt-3 border-t border-gray-100">
                                        <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.materials.show', [$course, $material]) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100"
                                            title="Lihat">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.materials.edit', [$course, $material]) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-100"
                                            title="Edit">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <form
                                            action="{{ route(auth()->user()->getRolePrefix() . '.courses.materials.destroy', [$course, $material]) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirmDelete('{{ __('Are you sure?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100"
                                                title="Hapus">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($materials->hasPages())
                            <div class="mt-4">
                                {{ $materials->links() }}
                            </div>
                        @endif
                    @else
                        <div class="flex flex-col items-center justify-center text-gray-500 py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-folder-open text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-sm font-semibold mb-2">{{ __('No materials yet') }}</p>
                            <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.materials.create', $course) }}"
                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                {{ __('Add your first material') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
