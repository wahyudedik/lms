@extends('errors.layout')

@section('title', 'Sesi Berakhir (419)')
@section('code', '419')
@section('icon-bg', 'from-orange-500 to-amber-600')
@section('badge-bg', 'bg-orange-50 text-orange-800')

@section('icon')
    <i class="fas fa-clock-rotate-left animate-spin-slow"></i>
@endsection

@section('message', 'Sesi Telah Berakhir')

@section('description')
    Maaf, halaman ini telah kedaluwarsa karena tidak ada aktivitas dalam waktu lama. Silakan klik tombol di bawah untuk menyegarkan halaman dan mencoba lagi.
@endsection

@section('action')
    <button onclick="window.location.reload();" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-600 text-white font-bold rounded-xl hover:from-orange-600 hover:to-amber-700 shadow-md shadow-orange-100 hover:shadow-lg transition-all duration-150 text-sm">
        <i class="fas fa-sync-alt"></i>
        Segarkan Sesi (Refresh)
    </button>
@endsection
