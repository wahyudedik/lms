@extends('errors.layout')

@section('title', 'Akses Tidak Sah (401)')
@section('code', '401')
@section('icon-bg', 'from-yellow-500 to-amber-600')
@section('badge-bg', 'bg-yellow-50 text-yellow-800')

@section('icon')
    <i class="fas fa-key animate-pulse"></i>
@endsection

@section('message', 'Akses Tidak Sah')

@section('description')
    Maaf, Anda tidak memiliki akses masuk ke halaman ini. Silakan pastikan Anda masuk (login) dengan akun yang memiliki wewenang yang tepat.
@endsection

@section('action')
    <a href="/login" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-yellow-500 to-amber-600 text-white font-bold rounded-xl hover:from-yellow-600 hover:to-amber-700 shadow-md shadow-amber-100 hover:shadow-lg transition-all duration-150 text-sm">
        <i class="fas fa-sign-in-alt"></i>
        Masuk (Login)
    </a>
@endsection
