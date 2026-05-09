<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-file-alt mr-2"></i>{{ __('Materials - :title', ['title' => $course->title]) }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Kode: {{ $course->code }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route(auth()->user()->getRolePrefix() . '.', $course) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-plus"></i>
                    {{ __('Add Material') }}
                </a>
                <a href="{{ route(auth()->user()->getRolePrefix() . '.', $course) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Materials Table -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Order</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        {{ __('Material') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Tipe</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Pembuat</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($materials as $material)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            #{{ $material->order }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <i
                                                    class="{{ $material->getFileIcon() }} {{ $material->getFileColorClass() }} text-2xl mr-3"></i>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">
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
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                {{ $material->type_display }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $material->creator->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                                {{ $material->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                <i
                                                    class="fas fa-{{ $material->is_published ? 'check' : 'clock' }} mr-1"></i>
                                                {{ $material->is_published ? 'Published' : 'Draft' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <div class="flex justify-end gap-3">
                                                <a href="{{ route(auth()->user()->getRolePrefix() . '.', [$course, $material]) }}"
                                                    class="text-blue-600 hover:text-blue-800 font-semibold">
                                                    Lihat
                                                </a>
                                                <a href="{{ route(auth()->user()->getRolePrefix() . '.', [$course, $material]) }}"
                                                    class="text-green-600 hover:text-green-800 font-semibold">
                                                    Edit
                                                </a>
                                                <form
                                                    action="{{ route(auth()->user()->getRolePrefix() . '.', [$course, $material]) }}"
                                                    method="POST" class="inline"
                                                    onsubmit="return confirmDelete('{{ __('Are you sure you want to delete this material?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800 font-semibold">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center text-gray-500">
                                                <div
                                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                    <i class="fas fa-folder-open text-3xl text-gray-400"></i>
                                                </div>
                                                <p class="text-sm font-semibold mb-2">{{ __('No materials yet') }}</p>
                                                <a href="{{ route(auth()->user()->getRolePrefix() . '.', $course) }}"
                                                    class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                                    {{ __('Add your first material') }}
                                                </a>
                                            </div>
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
