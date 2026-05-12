@props(['card' => null])

<!-- Attachment (PDF/DOC/PPT) -->
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Attachment') }} <span
            class="text-xs text-gray-400">({{ __('Optional') }})</span></label>
    <p class="text-xs text-gray-500 mb-2">{{ __('Upload PDF, DOC, or PPT file. Max 10MB.') }}</p>

    @if ($card && $card->hasAttachment())
        <div class="mb-3 flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
            <div
                class="w-10 h-10 flex items-center justify-center rounded-lg
                @if ($card->attachment_extension === 'pdf') bg-red-100 text-red-600
                @elseif(in_array($card->attachment_extension, ['doc', 'docx'])) bg-blue-100 text-blue-600
                @else bg-orange-100 text-orange-600 @endif">
                <i
                    class="fas
                    @if ($card->attachment_extension === 'pdf') fa-file-pdf
                    @elseif(in_array($card->attachment_extension, ['doc', 'docx'])) fa-file-word
                    @else fa-file-powerpoint @endif text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">{{ $card->attachment_name }}</p>
                <p class="text-xs text-gray-500">{{ $card->attachment_size_formatted }}</p>
            </div>
            <label class="inline-flex items-center gap-1 text-xs text-red-600 cursor-pointer hover:text-red-800">
                <input type="checkbox" name="remove_attachment" value="1"
                    class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                <span>{{ __('Remove') }}</span>
            </label>
        </div>
    @endif

    <input type="file" name="attachment" accept=".pdf,.doc,.docx,.ppt,.pptx"
        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
    @error('attachment')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<!-- Video URL -->
<div>
    <label for="video_url" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Video URL') }} <span
            class="text-xs text-gray-400">({{ __('Optional') }})</span></label>
    <p class="text-xs text-gray-500 mb-2">{{ __('YouTube or video link for embedded preview.') }}</p>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fab fa-youtube text-red-500"></i>
        </div>
        <input type="url" name="video_url" id="video_url" value="{{ old('video_url', $card?->video_url) }}"
            class="w-full pl-10 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
            placeholder="https://www.youtube.com/watch?v=...">
    </div>
    @error('video_url')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
