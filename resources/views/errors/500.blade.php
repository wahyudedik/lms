@extends('errors.layout')

@section('title', 'Kesalahan Internal Server (500)')
@section('code', '500')
@section('icon-bg', 'from-red-600 to-rose-700')
@section('badge-bg', 'bg-red-100 text-red-800')

@section('icon')
    <i class="fas fa-server"></i>
@endsection

@section('message', 'Terjadi Kesalahan Sistem')

@section('description')
    Maaf, terjadi masalah internal pada server kami saat memproses permintaan Anda. Kami telah mencatat kesalahan ini dan akan segera memperbaikinya. Silakan coba memuat ulang halaman.
@endsection

@section('action')
    <button onclick="window.location.reload();" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-red-600 to-rose-700 text-white font-bold rounded-xl hover:from-red-700 hover:to-rose-800 shadow-md shadow-rose-100 hover:shadow-lg transition-all duration-150 text-sm">
        <i class="fas fa-sync-alt"></i>
        Muat Ulang Halaman
    </button>
@endsection
