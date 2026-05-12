@props(['cards'])

@if ($cards->isNotEmpty())
    <div class="space-y-3">
        @foreach ($cards as $card)
            <div class="rounded-lg border p-4 {{ $card->card_color_class }}">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <i class="{{ $card->icon ?? 'fas fa-info-circle' }} {{ $card->icon_color_class }} text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-sm mb-1">{{ $card->title }}</h4>
                        <div
                            class="text-sm opacity-90 [&>p]:mb-1 [&>ul]:mb-1 [&>ul]:list-disc [&>ul]:pl-4 [&>ol]:mb-1 [&>ol]:list-decimal [&>ol]:pl-4 [&_a]:underline">
                            {!! $card->content !!}</div>

                        {{-- Attachment --}}
                        @if ($card->hasAttachment())
                            <div
                                class="mt-3 inline-flex items-center gap-2 px-3 py-2 bg-white/60 border border-current/10 rounded-lg">
                                <i
                                    class="fas
                                    @if ($card->attachment_extension === 'pdf') fa-file-pdf text-red-500
                                    @elseif(in_array($card->attachment_extension, ['doc', 'docx'])) fa-file-word text-blue-500
                                    @else fa-file-powerpoint text-orange-500 @endif"></i>
                                <span
                                    class="text-xs font-medium truncate max-w-[150px]">{{ $card->attachment_name }}</span>
                                <span class="text-xs opacity-60">({{ $card->attachment_size_formatted }})</span>
                                <a href="{{ $card->attachment_url }}" target="_blank" download
                                    class="text-xs font-semibold underline hover:no-underline ml-1">
                                    {{ __('Download') }}
                                </a>
                            </div>
                        @endif

                        {{-- Video --}}
                        @if ($card->hasVideo())
                            <div class="mt-3">
                                <div class="aspect-video rounded-lg overflow-hidden border border-current/10 max-w-md">
                                    <iframe src="{{ $card->video_embed_url }}" class="w-full h-full" frameborder="0"
                                        allowfullscreen
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                                </div>
                            </div>
                        @endif

                        @if ($card->schedule_type === 'date_range')
                            <p class="text-xs opacity-70 mt-2">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                {{ $card->start_date->translatedFormat('d M Y') }} -
                                {{ $card->end_date->translatedFormat('d M Y') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
