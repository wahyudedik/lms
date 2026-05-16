<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-users-cog mr-2"></i>Kelompok - {{ $course->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Kode: {{ $course->code }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.show', $course) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span class="hidden sm:inline">Kembali</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 text-red-800 font-semibold text-sm mb-1">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Terjadi kesalahan:</span>
                    </div>
                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Create New Group --}}
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-plus-circle text-green-600 mr-2"></i>Buat Kelompok Baru
                    </h3>
                    <form action="{{ route(auth()->user()->getRolePrefix() . '.courses.groups.store', $course) }}"
                        method="POST" class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama kelompok..."
                            class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            required maxlength="255">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-plus"></i>
                            Buat Kelompok
                        </button>
                    </form>
                </div>
            </div>

            {{-- Groups List --}}
            @if ($groups->count() > 0)
                <div class="space-y-6">
                    @foreach ($groups as $group)
                        <div class="bg-white overflow-hidden shadow-md rounded-lg" x-data="{ showMembers: false, editing: false, editName: '{{ addslashes($group->name) }}' }">
                            <div class="p-4 sm:p-6">
                                {{-- Group Header --}}
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-layer-group text-blue-600"></i>
                                        </div>
                                        <div>
                                            {{-- Display Name --}}
                                            <h4 class="text-lg font-bold text-gray-900" x-show="!editing">
                                                {{ $group->name }}
                                            </h4>
                                            {{-- Edit Form --}}
                                            <form x-show="editing" x-cloak
                                                action="{{ route(auth()->user()->getRolePrefix() . '.courses.groups.update', [$course, $group]) }}"
                                                method="POST" class="flex items-center gap-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="name" x-model="editName"
                                                    class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-1"
                                                    required maxlength="255">
                                                <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-100"
                                                    title="Simpan">
                                                    <i class="fas fa-check text-sm"></i>
                                                </button>
                                                <button type="button"
                                                    x-on:click="editing = false; editName = '{{ addslashes($group->name) }}'"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-100"
                                                    title="Batal">
                                                    <i class="fas fa-times text-sm"></i>
                                                </button>
                                            </form>
                                            <p class="text-sm text-gray-500">
                                                <i class="fas fa-users mr-1"></i>{{ $group->members->count() }} anggota
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" x-on:click="showMembers = !showMembers"
                                            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                            <i class="fas fa-users"></i>
                                            <span x-text="showMembers ? 'Sembunyikan' : 'Kelola Anggota'"></span>
                                        </button>
                                        <button type="button" x-on:click="editing = !editing" x-show="!editing"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition-colors"
                                            title="Edit nama">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <form
                                            action="{{ route(auth()->user()->getRolePrefix() . '.courses.groups.destroy', [$course, $group]) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirmDelete('Apakah Anda yakin ingin menghapus kelompok &quot;{{ $group->name }}&quot;? Semua data anggota kelompok ini akan dihapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                                title="Hapus kelompok">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Member Management Section --}}
                                <div x-show="showMembers" x-cloak x-transition
                                    class="border-t border-gray-200 pt-4 mt-4">

                                    {{-- Add Member Form --}}
                                    <div class="mb-4">
                                        <h5 class="text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-user-plus text-green-600 mr-1"></i>Tambah Anggota
                                        </h5>
                                        <form
                                            action="{{ route(auth()->user()->getRolePrefix() . '.courses.groups.members.store', [$course, $group]) }}"
                                            method="POST" class="flex flex-col sm:flex-row gap-2">
                                            @csrf
                                            <select name="user_id"
                                                class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                                required>
                                                <option value="">-- Pilih Siswa --</option>
                                                @foreach ($enrolledStudents as $student)
                                                    @unless (in_array($student->id, $allGroupedStudentIds))
                                                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                                                    @endunless
                                                @endforeach
                                            </select>
                                            <button type="submit"
                                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm text-sm">
                                                <i class="fas fa-plus"></i>
                                                Tambah
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Members List --}}
                                    <div>
                                        <h5 class="text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-list text-blue-600 mr-1"></i>Daftar Anggota
                                        </h5>
                                        @if ($group->members->count() > 0)
                                            <div class="space-y-2">
                                                @foreach ($group->members->sortBy('name') as $member)
                                                    <div
                                                        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                                                        <div class="flex items-center gap-3">
                                                            <div
                                                                class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                                <i class="fas fa-user text-blue-600 text-xs"></i>
                                                            </div>
                                                            <span
                                                                class="text-sm font-medium text-gray-900">{{ $member->name }}</span>
                                                        </div>
                                                        <form
                                                            action="{{ route(auth()->user()->getRolePrefix() . '.courses.groups.members.destroy', [$course, $group, $member]) }}"
                                                            method="POST" class="inline"
                                                            onsubmit="return confirmDelete('Apakah Anda yakin ingin menghapus {{ $member->name }} dari kelompok ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                                                title="Hapus dari kelompok">
                                                                <i class="fas fa-user-minus text-xs"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="flex flex-col items-center justify-center text-gray-500 py-6">
                                                <div
                                                    class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                                    <i class="fas fa-users text-gray-400 text-lg"></i>
                                                </div>
                                                <p class="text-sm">Belum ada anggota dalam kelompok ini.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-md rounded-lg">
                    <div class="p-6">
                        <div class="flex flex-col items-center justify-center text-gray-500 py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-users-cog text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-sm font-semibold mb-2">Belum ada kelompok</p>
                            <p class="text-xs text-gray-400">Buat kelompok pertama menggunakan form di atas.</p>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
