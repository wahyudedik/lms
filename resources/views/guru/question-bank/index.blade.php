<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-database mr-2"></i>{{ __('Bank Soal Saya') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ __('Kelola koleksi soal untuk digunakan di berbagai ujian') }}
                </p>
            </div>
            <a href="{{ route(auth()->user()->getRolePrefix() . '.question-bank.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-plus"></i>
                {{ __('Tambah Soal') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg mb-6">
                <div class="p-4 sm:p-6">
                    <form method="GET" action="{{ route(auth()->user()->getRolePrefix() . '.question-bank.index') }}"
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari soal..."
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <select name="category_id"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="type"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Tipe</option>
                                <option value="mcq_single" {{ request('type') == 'mcq_single' ? 'selected' : '' }}>MCQ
                                    Single</option>
                                <option value="mcq_multiple" {{ request('type') == 'mcq_multiple' ? 'selected' : '' }}>
                                    MCQ Multiple</option>
                                <option value="matching" {{ request('type') == 'matching' ? 'selected' : '' }}>Matching
                                </option>
                                <option value="essay" {{ request('type') == 'essay' ? 'selected' : '' }}>Essay
                                </option>
                            </select>
                        </div>
                        <div>
                            <select name="difficulty"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Kesulitan</option>
                                <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Mudah
                                </option>
                                <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Sedang
                                </option>
                                <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Sulit
                                </option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route(auth()->user()->getRolePrefix() . '.question-bank.index') }}"
                                class="inline-flex items-center justify-center px-3 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Questions List -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-4 sm:p-6">
                    @if ($questions->count() > 0)
                        <p class="text-sm text-gray-500 mb-4">{{ $questions->total() }} soal ditemukan</p>
                        <div class="space-y-3">
                            @foreach ($questions as $question)
                                <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 mb-2">
                                                {{ Str::limit($question->question_text, 120) }}
                                            </p>
                                            <div class="flex flex-wrap gap-2">
                                                @php
                                                    $typeColors = [
                                                        'mcq_single' => 'bg-blue-100 text-blue-800',
                                                        'mcq_multiple' => 'bg-purple-100 text-purple-800',
                                                        'matching' => 'bg-indigo-100 text-indigo-800',
                                                        'essay' => 'bg-pink-100 text-pink-800',
                                                    ];
                                                    $diffColors = [
                                                        'easy' => 'bg-green-100 text-green-800',
                                                        'medium' => 'bg-yellow-100 text-yellow-800',
                                                        'hard' => 'bg-red-100 text-red-800',
                                                    ];
                                                @endphp
                                                <span
                                                    class="px-2 py-0.5 text-xs font-semibold rounded {{ $typeColors[$question->type] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ strtoupper(str_replace('_', ' ', $question->type)) }}
                                                </span>
                                                <span
                                                    class="px-2 py-0.5 text-xs font-semibold rounded {{ $diffColors[$question->difficulty] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst($question->difficulty) }}
                                                </span>
                                                @if ($question->category)
                                                    <span
                                                        class="px-2 py-0.5 text-xs font-semibold rounded bg-gray-100 text-gray-700">
                                                        {{ $question->category->name }}
                                                    </span>
                                                @endif
                                                <span class="px-2 py-0.5 text-xs rounded bg-yellow-50 text-yellow-700">
                                                    <i class="fas fa-star mr-1"></i>{{ $question->default_points }}
                                                    poin
                                                </span>
                                                @if ($question->is_active)
                                                    <span
                                                        class="px-2 py-0.5 text-xs font-semibold rounded bg-green-100 text-green-700">
                                                        Aktif
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 py-0.5 text-xs font-semibold rounded bg-red-100 text-red-700">
                                                        Nonaktif
                                                    </span>
                                                @endif
                                                @if ($question->is_shared)
                                                    <span
                                                        class="px-2 py-0.5 text-xs font-semibold rounded bg-indigo-100 text-indigo-700">
                                                        <i class="fas fa-share-alt mr-1"></i>Shared
                                                    </span>
                                                @endif
                                                @if ($question->times_used > 0)
                                                    <span class="px-2 py-0.5 text-xs rounded bg-blue-50 text-blue-700">
                                                        <i class="fas fa-recycle mr-1"></i>Digunakan
                                                        {{ $question->times_used }}x
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 ml-4">
                                            <a href="{{ route(auth()->user()->getRolePrefix() . '.question-bank.edit', $question) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition-colors"
                                                title="Edit">
                                                <i class="fas fa-edit text-sm"></i>
                                            </a>
                                            <form
                                                action="{{ route(auth()->user()->getRolePrefix() . '.question-bank.duplicate', $question) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors"
                                                    title="Duplikasi">
                                                    <i class="fas fa-copy text-sm"></i>
                                                </button>
                                            </form>
                                            <form
                                                action="{{ route(auth()->user()->getRolePrefix() . '.question-bank.destroy', $question) }}"
                                                method="POST" class="inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                                    title="Hapus">
                                                    <i class="fas fa-trash text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($questions->hasPages())
                            <div class="mt-6">
                                {{ $questions->links() }}
                            </div>
                        @endif
                    @else
                        <div class="flex flex-col items-center justify-center text-gray-500 py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-database text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-lg font-semibold mb-2">{{ __('Belum ada soal di bank soal') }}</p>
                            <p class="text-sm text-gray-400 mb-4">
                                {{ __('Mulai tambahkan soal untuk digunakan di berbagai ujian') }}</p>
                            <a href="{{ route(auth()->user()->getRolePrefix() . '.question-bank.create') }}"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all">
                                <i class="fas fa-plus"></i>
                                {{ __('Tambah Soal Pertama') }}
                            </a>
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
                    Swal.fire({
                        title: 'Hapus Soal?',
                        text: 'Soal yang dihapus tidak dapat dikembalikan!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
