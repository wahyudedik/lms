@extends('errors.layout')

@section('title', 'Akses Ditolak (403)')
@section('code', '403')
@section('icon-bg', 'from-red-500 to-rose-600')
@section('badge-bg', 'bg-red-50 text-red-800')

@section('icon')
    <i class="fas fa-user-shield"></i>
@endsection

@section('message', 'Akses Ditolak')

@section('description')
    Maaf, Anda tidak memiliki izin atau wewenang untuk membuka halaman ini. Silakan hubungi admin sekolah jika Anda memerlukan akses ke halaman tersebut.
@endsection

@section('action')
    <!-- Default layout home button will serve as fallback, no additional special action is needed -->
@endsection
