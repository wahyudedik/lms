<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $material->title }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.courses.materials.edit', [$course, $material]) }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i>{{ __('Edit') }}
                </a>
                <a href="{{ route('admin.courses.materials.index', $course) }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
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
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <i
                                    class="{{ $material->getFileIcon() }} {{ $material->getFileColorClass() }} text-4xl mr-4"></i>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $material->title }}</h3>
                                    <p class="text-sm text-gray-500">{{ __($material->type_display) }}</p>
                                </div>
                            </div>

                            @if ($material->description)
                                <div class="mb-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Description') }}</h4>
                                    <p class="text-gray-700 whitespace-pre-wrap">{{ $material->description }}</p>
                                </div>
                            @endif

                            <!-- Material Display -->
                            @if ($material->type === 'file' || $material->type === 'video')
                                <div class="mb-6">
                                    @if ($material->getFileUrl())
                                        <a href="{{ $material->getFileUrl() }}" download
                                            class="inline-flex items-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded">
                                            <i class="fas fa-download mr-2"></i>
                                            {{ __('Download :filename', ['filename' => $material->file_name]) }}
                                            @if ($material->getFormattedFileSize())
                                                ({{ $material->getFormattedFileSize() }})
                                            @endif
                                        </a>
                                    @endif
                                </div>
                            @elseif($material->type === 'youtube')
                                @if ($material->getEmbedUrl())
                                    <div class="mb-6">
                                        <div class="aspect-w-16 aspect-h-9">
                                            <iframe src="{{ $material->getEmbedUrl() }}" frameborder="0"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                allowfullscreen class="w-full h-96 rounded"></iframe>
                                        </div>
                                    </div>
                                @endif
                            @elseif($material->type === 'link')
                                <div class="mb-6">
                                    <a href="{{ $material->url }}" target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded">
                                        <i class="fas fa-external-link-alt mr-2"></i>
                                        {{ __('Open Link') }}
                                    </a>
                                    <p class="mt-2 text-sm text-gray-500">{{ $material->url }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                {{ __('Discussion (:count comments)', ['count' => $material->allComments->count()]) }}
                            </h3>

                            <!-- Comment Form -->
                            <form action="{{ route('materials.comments.store', $material) }}" method="POST"
                                class="mb-6">
                                @csrf
                                <textarea name="comment" rows="3" required placeholder="{{ __('Write a comment...') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                <button type="submit"
                                    class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    <i class="fas fa-comment mr-2"></i>{{ __('Post Comment') }}
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
                                                        class="text-red-600 hover:text-red-900 text-sm">
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
                                                                    class="text-red-600 hover:text-red-900 text-sm">
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
                                                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                <button type="submit"
                                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                    <i class="fas fa-reply mr-1"></i>{{ __('Reply') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center py-4">
                                        {{ __('No comments yet. Be the first to comment!') }}
                                    </p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Material Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Information') }}</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm text-gray-500">{{ __('Course') }}</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $course->title }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">{{ __('Dibuat oleh') }}</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $material->creator->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">{{ __('Status') }}</dt>
                                    <dd>
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $material->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $material->is_published ? __('Published') : __('Draft') }}
                                        </span>
                                    </dd>
                                </div>
                                @if ($material->published_at)
                                    <div>
                                        <dt class="text-sm text-gray-500">{{ __('Dipublikasikan') }}</dt>
                                        <dd class="text-sm font-medium text-gray-900">
                                            {{ $material->published_at->format('d M Y H:i') }}</dd>
                                    </div>
                                @endif
                                <div>
                                    <dt class="text-sm text-gray-500">{{ __('Dibuat') }}</dt>
                                    <dd class="text-sm font-medium text-gray-900">
                                        {{ $material->created_at->format('d M Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Aksi') }}</h3>
                            <div class="space-y-2">
                                <form
                                    action="{{ route('admin.courses.materials.toggle-status', [$course, $material]) }}"
                                    method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        @if ($material->is_published)
                                            <i class="fas fa-eye-slash mr-2"></i>{{ __('Unpublish') }}
                                        @else
                                            <i class="fas fa-check mr-2"></i>{{ __('Publish') }}
                                        @endif
                                    </button>
                                </form>

                                <form action="{{ route('admin.courses.materials.destroy', [$course, $material]) }}"
                                    method="POST"
                                    onsubmit="return confirmDelete('{{ __('Are you sure you want to delete this material?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        <i class="fas fa-trash mr-2"></i>{{ __('Delete Material') }}
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
