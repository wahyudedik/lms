<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-folder-open mr-2"></i>Kategori Bank Soal
            </h2>
            <a href="{{ route('admin.question-bank-categories.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-plus"></i>
                Tambah Kategori
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">

                    @if ($categories->isEmpty())
                        <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                            <i class="fas fa-folder-open text-5xl mb-4"></i>
                            <p class="text-lg font-semibold">Belum ada kategori</p>
                            <p class="text-sm mt-1">Tambahkan kategori untuk mengorganisir soal di bank soal.</p>
                            <a href="{{ route('admin.question-bank-categories.create') }}"
                                class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200">
                                <i class="fas fa-plus"></i> Tambah Kategori Pertama
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Kategori</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Deskripsi</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Jumlah Soal</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($categories as $category)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-2">
                                                    <span class="inline-block w-3 h-3 rounded-full flex-shrink-0"
                                                        style="background-color: {{ $category->color }}"></span>
                                                    <span
                                                        class="font-semibold text-gray-900">{{ $category->name }}</span>
                                                </div>
                                                @if ($category->children->isNotEmpty())
                                                    <div class="mt-2 ml-5 space-y-1">
                                                        @foreach ($category->children as $child)
                                                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                                                <i
                                                                    class="fas fa-level-up-alt fa-rotate-90 text-gray-400 text-xs"></i>
                                                                <span
                                                                    class="inline-block w-2 h-2 rounded-full flex-shrink-0"
                                                                    style="background-color: {{ $child->color }}"></span>
                                                                {{ $child->name }}
                                                                <span
                                                                    class="text-xs text-gray-400">({{ $child->question_count }}
                                                                    soal)</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ $category->description ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                    {{ $category->questions_count }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                                    {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                                                <div class="flex gap-3">
                                                    <a href="{{ route('admin.question-bank-categories.edit', $category) }}"
                                                        class="text-green-600 hover:text-green-800">Edit</a>
                                                    <form method="POST"
                                                        action="{{ route('admin.question-bank-categories.destroy', $category) }}"
                                                        class="inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800"
                                                            data-name="{{ $category->name }}">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const name = this.querySelector('button').getAttribute('data-name');
                    confirmDelete(
                            `Hapus kategori "${name}"? Kategori yang masih memiliki soal tidak dapat dihapus.`)
                        .then(result => {
                            if (result.isConfirmed) this.submit();
                        });
                });
            });
        </script>
    @endpush
</x-app-layout>
