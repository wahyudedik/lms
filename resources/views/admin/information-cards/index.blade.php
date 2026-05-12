<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-id-card mr-2"></i>{{ __('Information Cards') }}
            </h2>
            <a href="{{ route('admin.information-cards.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>{{ __('Create Card') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Search') }}</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="{{ __('Search by title...') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="w-40">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                        <select name="status"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">{{ __('All') }}</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                                {{ __('Active') }}</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                                {{ __('Inactive') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-search mr-1"></i>{{ __('Filter') }}
                    </button>
                </form>
            </div>

            <!-- Cards List -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                @forelse($cards as $card)
                    <div class="p-4 border-b border-gray-200 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <i
                                        class="{{ $card->icon ?? 'fas fa-info-circle' }} {{ $card->icon_color_class }}"></i>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $card->title }}</h3>
                                    <span
                                        class="px-2 py-0.5 text-xs rounded-full {{ $card->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $card->is_active ? __('Active') : __('Inactive') }}
                                    </span>
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($card->card_type) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ Str::limit($card->content, 100) }}</p>
                                <div class="flex flex-wrap gap-2 text-xs text-gray-500">
                                    <span><i class="fas fa-users mr-1"></i>{{ __('Roles') }}:
                                        {{ implode(', ', $card->target_roles) }}</span>
                                    <span><i
                                            class="fas fa-calendar mr-1"></i>{{ ucfirst(str_replace('_', ' ', $card->schedule_type)) }}</span>
                                    @if ($card->schedule_type === 'date_range')
                                        <span>{{ $card->start_date->format('d/m/Y') }} -
                                            {{ $card->end_date->format('d/m/Y') }}</span>
                                    @endif
                                    @if ($card->target_user_ids)
                                        <span><i class="fas fa-user-tag mr-1"></i>{{ count($card->target_user_ids) }}
                                            {{ __('specific users') }}</span>
                                    @else
                                        <span><i class="fas fa-globe mr-1"></i>{{ __('All users in role') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 ml-4">
                                <form method="POST"
                                    action="{{ route('admin.information-cards.toggle-status', $card) }}">
                                    @csrf
                                    <button type="submit"
                                        class="p-2 rounded-lg {{ $card->is_active ? 'text-green-600 hover:bg-green-50' : 'text-gray-400 hover:bg-gray-100' }}"
                                        title="{{ $card->is_active ? __('Deactivate') : __('Activate') }}">
                                        <i class="fas fa-toggle-{{ $card->is_active ? 'on' : 'off' }} text-xl"></i>
                                    </button>
                                </form>
                                <a href="{{ route('admin.information-cards.edit', $card) }}"
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="{{ __('Edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.information-cards.destroy', $card) }}"
                                    onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg"
                                        title="{{ __('Delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <i class="fas fa-id-card text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-500 mb-4">{{ __('No information cards yet.') }}</p>
                        <a href="{{ route('admin.information-cards.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>{{ __('Create First Card') }}
                        </a>
                    </div>
                @endforelse
            </div>

            @if ($cards->hasPages())
                <div class="mt-4">
                    {{ $cards->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
