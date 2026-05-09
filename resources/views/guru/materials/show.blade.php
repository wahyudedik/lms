<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-file-alt mr-2"></i>{{ $material->title }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route(auth()->user()->getRolePrefix() . '.', [$course, $material]) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-edit"></i>
                    Edit
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
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Material Content -->
                    <div class="bg-white overflow-hidden shadow-md rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <i
                                    class="{{ $material->getFileIcon() }} {{ $material->getFileColorClass() }} text-4xl mr-4"></i>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $material->title }}</h3>
                                    <p class="text-sm text-gray-500">{{ $material->type_display }}</p>
                                </div>
                            </div>

                            @if ($material->description)
                                <div class="mb-6">
                                    <h4 class="text-lg font-bold text-gray-900 mb-2">
                                        <i class="fas fa-align-left text-blue-600 mr-2"></i>Deskripsi
                                    </h4>
                                    <p class="text-gray-700 whitespace-pre-wrap">{{ $material->description }}</p>
                                </div>
                            @endif

                            <!-- Material Display -->
                            @if ($material->type === 'file' || $material->type === 'video')
                                <div class="mb-6">
                                    @if ($material->getFileUrl())
                                        <div class="flex flex-wrap gap-3">
                                            @if ($material->isPreviewable())
                                                <a href="{{ $material->getFileUrl() }}" target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                                    <i class="fas fa-eye"></i>
                                                    Preview
                                                </a>
                                            @endif
                                            <a href="{{ $material->getFileUrl() }}" download
                                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                                <i class="fas fa-download"></i>
                                                Download {{ $material->file_name }}
                                                @if ($material->getFormattedFileSize())
                                                    ({{ $material->getFormattedFileSize() }})
                                                @endif
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @elseif($material->type === 'youtube')
                                @if ($material->getEmbedUrl())
                                    <div class="mb-6">
                                        <div class="aspect-w-16 aspect-h-9">
                                            <iframe src="{{ $material->getEmbedUrl() }}" frameborder="0"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                allowfullscreen class="w-full h-96 rounded-lg"></iframe>
                                        </div>
                                    </div>
                                @endif
                            @elseif($material->type === 'link')
                                <div class="mb-6">
                                    <a href="{{ $material->url }}" target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                        <i class="fas fa-external-link-alt"></i>
                                        Buka Link
                                    </a>
                                    <p class="mt-2 text-sm text-gray-500">{{ $material->url }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="bg-white overflow-hidden shadow-md rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-comments text-purple-600 mr-2"></i>Diskusi
                                ({{ $material->allComments->count() }} komentar)
                            </h3>

                            <!-- Comment Form -->
                            <form action="{{ route('materials.comments.store', $material) }}" method="POST"
                                class="mb-6">
                                @csrf
                                <textarea name="comment" rows="3" required placeholder="Tulis komentar..."
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150"></textarea>
                                <button type="submit"
                                    class="mt-2 inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fas fa-comment"></i>
                                    {{ __('Post Comment') }}
                                </button>
                            </form>

                            <!-- Comments List -->
                            <div class="space-y-4">
                                @forelse($material->comments as $comment)
                                    <div class="border-l-4 border-blue-500 pl-4 py-2">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center mb-1">
                                                    <img src="{{ $comment->user->profile_photo_url }}"
                                                        class="w-8 h-8 rounded-full mr-2">
                                                    <div>
                                                        <p class="text-sm font-semibold text-gray-900">
                                                            {{ $comment->user->name }}</p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ $comment->created_at->diffForHumans() }}</p>
                                                    </div>
                                                </div>
                                                <p class="text-gray-700">{{ $comment->comment }}</p>
                                            </div>
                                            @if ($comment->canDelete(auth()->user()))
                                                <form action="{{ route('comments.destroy', $comment) }}" method="POST"
                                                    class="ml-4"
                                                    onsubmit="return confirmDelete('{{ __('Are you sure you want to delete this comment?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800 text-sm font-semibold">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                        <!-- Replies -->
                                        @if ($comment->replies->count() > 0)
                                            <div class="mt-3 ml-6 space-y-3">
                                                @foreach ($comment->replies as $reply)
                                                    <div class="flex items-start justify-between">
                                                        <div class="flex-1">
                                                            <div class="flex items-center mb-1">
                                                                <img src="{{ $reply->user->profile_photo_url }}"
                                                                    class="w-6 h-6 rounded-full mr-2">
                                                                <div>
                                                                    <p class="text-sm font-semibold text-gray-900">
                                                                        {{ $reply->user->name }}</p>
                                                                    <p class="text-xs text-gray-500">
                                                                        {{ $reply->created_at->diffForHumans() }}</p>
                                                                </div>
                                                            </div>
                                                            <p class="text-sm text-gray-700">{{ $reply->comment }}</p>
                                                        </div>
                                                        @if ($reply->canDelete(auth()->user()))
                                                            <form action="{{ route('comments.destroy', $reply) }}"
                                                                method="POST" class="ml-4"
                                                                onsubmit="return confirmDelete('{{ __('Are you sure you want to delete this reply?') }}');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="text-red-600 hover:text-red-800 text-sm font-semibold">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Reply Form -->
                                        <form action="{{ route('materials.comments.store', $material) }}"
                                            method="POST" class="mt-3 ml-6">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                            <div class="flex gap-2">
                                                <input type="text" name="comment" required
                                                    placeholder="{{ __('Write a reply...') }}"
                                                    class="flex-1 px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150 text-sm">
                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-sm text-sm">
                                                    <i class="fas fa-reply"></i>
                                                    {{ __('Reply') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @empty
                                    <div class="flex flex-col items-center justify-center text-gray-500 py-8">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-comments text-3xl text-gray-400"></i>
                                        </div>
                                        <p class="text-sm font-semibold">
                                            {{ __('No comments yet. Be the first to comment!') }}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Material Info -->
                    <div class="bg-white overflow-hidden shadow-md rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>Informasi
                            </h3>
                            <dl class="space-y-3">
                                <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                                    <dt class="text-xs font-semibold text-blue-700 mb-1">{{ __('Course') }}</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $course->title }}</dd>
                                </div>
                                <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
                                    <dt class="text-xs font-semibold text-purple-700 mb-1">Dibuat oleh</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $material->creator->name }}
                                    </dd>
                                </div>
                                <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                                    <dt class="text-xs font-semibold text-green-700 mb-1">Status</dt>
                                    <dd>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                            {{ $material->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            <i
                                                class="fas fa-{{ $material->is_published ? 'check' : 'clock' }} mr-1"></i>
                                            {{ $material->is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </dd>
                                </div>
                                @if ($material->published_at)
                                    <div class="p-3 bg-orange-50 rounded-lg border border-orange-100">
                                        <dt class="text-xs font-semibold text-orange-700 mb-1">Dipublikasikan</dt>
                                        <dd class="text-sm font-semibold text-gray-900">
                                            {{ $material->published_at->format('d M Y H:i') }}</dd>
                                    </div>
                                @endif
                                <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-100">
                                    <dt class="text-xs font-semibold text-indigo-700 mb-1">Dibuat</dt>
                                    <dd class="text-sm font-semibold text-gray-900">
                                        {{ $material->created_at->format('d M Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white overflow-hidden shadow-md rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-cog text-gray-600 mr-2"></i>Aksi
                            </h3>
                            <div class="space-y-2">
                                <form
                                    action="{{ route(auth()->user()->getRolePrefix() . '.', [$course, $material]) }}"
                                    method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                        @if ($material->is_published)
                                            <i class="fas fa-eye-slash"></i>
                                            Unpublish
                                        @else
                                            <i class="fas fa-check"></i>
                                            Publish
                                        @endif
                                    </button>
                                </form>

                                <form action="{{ route(auth()->user()->getRolePrefix() . '.', [$course, $material]) }}"
                                    method="POST"
                                    onsubmit="return confirmDelete('{{ __('Are you sure you want to delete this material?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                        <i class="fas fa-trash"></i>
                                        {{ __('Delete Material') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
