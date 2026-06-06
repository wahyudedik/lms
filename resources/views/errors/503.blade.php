@extends('errors.layout')

@section('title', 'Layanan Tidak Tersedia (503)')
@section('code', '503')
@section('icon-bg', 'from-blue-600 to-indigo-700')
@section('badge-bg', 'bg-blue-100 text-blue-800')

@section('icon')
    <i class="fas fa-screwdriver-wrench"></i>
@endsection

@section('message', 'Pemeliharaan Sistem')

@section('description')
    Layanan LMS sedang dinonaktifkan sementara untuk pemeliharaan rutin atau peningkatan performa. Kami akan segera kembali aktif. Terima kasih atas kesabaran Anda.
@endsection

@section('action')
    <button onclick="window.location.reload();" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-bold rounded-xl hover:from-blue-700 hover:to-indigo-800 shadow-md shadow-indigo-100 hover:shadow-lg transition-all duration-150 text-sm">
        <i class="fas fa-sync-alt"></i>
        Coba Hubungkan Kembali
    </button>
@endsection
