<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-book-open mr-2"></i>{{ $material->title }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.courses.materials.edit', [$course, $material]) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-edit"></i>
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('admin.courses.materials.index', $course) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
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
                            <div class="flex items-center mb-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                                <div class="p-3 bg-blue-100 rounded-lg mr-4">
                                    <i class="{{ $material->getFileIcon() }} {{ $material->getFileColorClass() }} text-4xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $material->title }}</h3>
                                    <p class="text-sm font-semibold text-gray-600 mt-1">
                                        <i class="fas fa-tag text-gray-400 mr-1"></i>{{ __($material->type_display) }}
                                    </p>
                                </div>
                            </div>

                            @if ($material->description)
                                <div class="mb-6 p-4 bg-green-50 rounded-lg border border-green-100">
                                    <h4 class="text-lg font-bold text-gray-900 mb-2">
                                        <i class="fas fa-align-left text-green-600 mr-2"></i>{{ __('Description') }}
                                    </h4>
                                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $material->description }}</p>
                                </div>
                            @endif

                            <!-- Material Display -->
                            @if ($material->type === 'file' || $material->type === 'video')
                                <div class="mb-6">
                                    @if ($material->getFileUrl())
                                        <a href="{{ $material->getFileUrl() }}" download
                                            class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                            <i class="fas fa-download"></i>
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
                                        <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden shadow-md border border-gray-200">
                                            <iframe src="{{ $material->getEmbedUrl() }}" frameborder="0"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                allowfullscreen class="w-full h-96"></iframe>
                                        </div>
                                    </div>
                                @endif
                            @elseif($material->type === 'link')
                                <div class="mb-6 p-4 bg-purple-50 rounded-lg border border-purple-100">
                                    <a href="{{ $material->url }}" target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                        <i class="fas fa-external-link-alt"></i>
                                        {{ __('Open Link') }}
                                    </a>
                                    <p class="mt-3 text-sm text-gray-600 break-all">
                                        <i class="fas fa-link text-purple-600 mr-1"></i>{{ $material->url }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="bg-white overflow-hidden shadow-md rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-comments text-orange-600 mr-2"></i>{{ __('Discussion (:count comments)', ['count' => $material->allComments->count()]) }}
                            </h3>

                            <!-- Comment Form -->
                            <form action="{{ route('materials.comments.store', $material) }}" method="POST"
                                class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                @csrf
                                <label for="comment" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-pen text-gray-400 mr-1"></i>{{ __('Write a comment...') }}
                                </label>
                                <textarea name="comment" id="comment" rows="3" required
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150"></textarea>
                                <button type="submit"
                                    class="mt-3 inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fas fa-comment"></i>{{ __('Post Comment') }}
                                </button>
                            </form>

                            <!-- Comments List -->
                            <div class="space-y-4">
                                @forelse($material->comments as $comment)
                                    <div class="border-l-4 border-blue-600 pl-4 py-3 bg-blue-50 rounded-r-lg">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center mb-2">
                                                    <img src="{{ $comment->user->profile_photo_url }}"
                                                        class="w-8 h-8 rounded-full mr-2 border-2 border-blue-200">
                                                    <div>
                                                        <p class="text-sm font-semibold text-gray-900">
                                                            {{ $comment->user->name }}</p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ $comment->created_at->diffForHumans() }}</p>
                                                    </div>
                                                </div>
                                                <p class="text-sm text-gray-700">{{ $comment->comment }}</p>
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
                                                    <div class="flex items-start justify-between p-3 bg-white rounded-lg border border-blue-200">
                                                        <div class="flex-1">
                                                            <div class="flex items-center mb-1">
                                                                <img src="{{ $reply->user->profile_photo_url }}"
                                                                    class="w-6 h-6 rounded-full mr-2 border border-gray-200">
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
                                                    class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150 text-sm">
                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-sm text-sm">
                                                    <i class="fas fa-reply"></i>{{ __('Reply') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @empty
                                    <div class="flex flex-col items-center justify-center text-gray-500 py-8">
                                        <i class="fas fa-comments text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-sm font-semibold">{{ __('No comments yet. Be the first to comment!') }}</p>
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
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>{{ __('Information') }}
                            </h3>
                            <dl class="space-y-4">
                                <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                                    <dt class="text-xs font-semibold text-blue-700 mb-1">{{ __('Course') }}</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $course->title }}</dd>
                                </div>
                                <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                                    <dt class="text-xs font-semibold text-green-700 mb-1">{{ __('Dibuat oleh') }}</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $material->creator->name }}</dd>
                                </div>
                                <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
                                    <dt class="text-xs font-semibold text-purple-700 mb-1">{{ __('Status') }}</dt>
                                    <dd>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $material->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            <i class="fas fa-{{ $material->is_published ? 'check' : 'clock' }} mr-1"></i>
                                            {{ $material->is_published ? __('Published') : __('Draft') }}
                                        </span>
                                    </dd>
                                </div>
                                @if ($material->published_at)
                                    <div class="p-3 bg-orange-50 rounded-lg border border-orange-100">
                                        <dt class="text-xs font-semibold text-orange-700 mb-1">{{ __('Dipublikasikan') }}</dt>
                                        <dd class="text-sm font-semibold text-gray-900">
                                            {{ $material->published_at->format('d M Y H:i') }}</dd>
                                    </div>
                                @endif
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <dt class="text-xs font-semibold text-gray-700 mb-1">{{ __('Dibuat') }}</dt>
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
                                <i class="fas fa-bolt text-orange-600 mr-2"></i>{{ __('Aksi') }}
                            </h3>
                            <div class="space-y-3">
                                <form
                                    action="{{ route('admin.courses.materials.toggle-status', [$course, $material]) }}"
                                    method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                        @if ($material->is_published)
                                            <i class="fas fa-eye-slash"></i>{{ __('Unpublish') }}
                                        @else
                                            <i class="fas fa-check"></i>{{ __('Publish') }}
                                        @endif
                                    </button>
                                </form>

                                <form action="{{ route('admin.courses.materials.destroy', [$course, $material]) }}"
                                    method="POST"
                                    onsubmit="return confirmDelete('{{ __('Are you sure you want to delete this material?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                        <i class="fas fa-trash"></i>{{ __('Delete Material') }}
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
