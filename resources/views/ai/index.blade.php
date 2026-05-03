<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-gradient-to-br from-purple-500 to-blue-500 rounded-lg">
                <i class="fas fa-robot text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">AI Assistant</h2>
                <p class="text-sm text-gray-600">Dapatkan bantuan instan untuk pembelajaran Anda dengan ChatGPT</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (!$status['enabled'])
                <!-- Disabled Warning -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl mt-0.5"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-yellow-900">AI Assistant Tidak Tersedia</h3>
                            <p class="text-sm text-yellow-700 mt-1">Silakan hubungi administrator untuk mengaktifkan
                                fitur ini.</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- New Conversation Card -->
                <div class="bg-gradient-to-r from-purple-500 to-blue-600 rounded-xl shadow-lg p-8 mb-8 text-white">
                    <div class="max-w-3xl mx-auto text-center">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 rounded-full mb-4">
                            <i class="fas fa-comments text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-2">Mulai Percakapan Baru</h3>
                        <p class="text-purple-100 mb-6">Tanyakan apa saja tentang kursus, konsep, atau materi
                            pembelajaran Anda</p>

                        <form id="newConversationForm" class="max-w-2xl mx-auto">
                            <div class="flex gap-3">
                                <input type="text" id="firstMessage" placeholder="Ketik pertanyaan Anda di sini..."
                                    class="flex-1 px-6 py-4 rounded-lg text-gray-900 border-0 focus:ring-4 focus:ring-white focus:ring-opacity-30"
                                    required>
                                <button type="submit"
                                    class="px-8 py-4 bg-white text-purple-600 rounded-lg hover:bg-purple-50 font-semibold shadow-lg hover:shadow-xl transition-all">
                                    <i class="fas fa-paper-plane mr-2"></i>Kirim
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Recent Conversations -->
                @if ($conversations->count() > 0)
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 mb-8">
                        <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                            <i class="fas fa-history text-gray-600"></i>
                            Percakapan Terbaru
                        </h3>
                        <div class="space-y-3">
                            @foreach ($conversations as $conversation)
                                <a href="{{ route('ai.conversation.show', $conversation) }}"
                                    class="block p-5 border border-gray-200 rounded-lg hover:shadow-md hover:border-purple-300 transition-all">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 mb-2">
                                                {{ $conversation->title ?? 'Percakapan #' . $conversation->id }}
                                            </h4>
                                            <div class="flex items-center gap-4 text-sm text-gray-500 flex-wrap">
                                                <span class="inline-flex items-center gap-1">
                                                    <i class="fas fa-message"></i>
                                                    {{ $conversation->message_count }} pesan
                                                </span>
                                                @if ($conversation->course)
                                                    <span class="inline-flex items-center gap-1">
                                                        <i class="fas fa-book"></i>
                                                        {{ $conversation->course->title }}
                                                    </span>
                                                @endif
                                                <span class="inline-flex items-center gap-1">
                                                    <i class="fas fa-clock"></i>
                                                    {{ $conversation->last_message_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right text-gray-400"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12 bg-gray-50 rounded-xl border border-gray-200 mb-8">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                            <i class="fas fa-comment-dots text-gray-400 text-4xl"></i>
                        </div>
                        <p class="text-gray-600 text-lg font-medium">Belum ada percakapan</p>
                        <p class="text-gray-500 text-sm mt-1">Mulai chat pertama Anda di atas!</p>
                    </div>
                @endif
            @endif

            <!-- Features Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                        <i class="fas fa-book-reader text-blue-600 text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Bantuan Kursus</h4>
                    <p class="text-sm text-gray-600">Dapatkan penjelasan tentang materi dan konsep kursus</p>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                        <i class="fas fa-lightbulb text-purple-600 text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Pemecahan Masalah</h4>
                    <p class="text-sm text-gray-600">Dapatkan panduan (bukan jawaban) untuk tugas dan masalah</p>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                        <i class="fas fa-clock text-green-600 text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Tersedia 24/7</h4>
                    <p class="text-sm text-gray-600">Belajar sesuai kecepatan Anda, kapan saja Anda butuh bantuan</p>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('newConversationForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const input = document.getElementById('firstMessage');
                const message = input.value.trim();

                if (!message) return;

                // Disable form
                input.disabled = true;
                const button = this.querySelector('button[type="submit"]');
                const originalButtonText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
                button.disabled = true;

                try {
                    const response = await fetch('{{ route('ai.conversation.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            message
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        window.location.href = `/ai/conversations/${data.conversation_id}`;
                    } else {
                        throw new Error(data.error || 'Gagal mengirim pesan');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Gagal mengirim pesan. Silakan coba lagi.'
                    });

                    // Re-enable form
                    input.disabled = false;
                    button.innerHTML = originalButtonText;
                    button.disabled = false;
                }
            });
        </script>
    @endpush
</x-app-layout>
