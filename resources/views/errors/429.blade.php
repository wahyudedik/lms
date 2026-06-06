@extends('errors.layout')

@section('title', 'Terlalu Banyak Permintaan (429)')
@section('code', '429')
@section('icon-bg', 'from-teal-500 to-emerald-600')
@section('badge-bg', 'bg-teal-50 text-teal-800')

@section('icon')
    <i class="fas fa-hand"></i>
@endsection

@section('message', 'Aktivitas Terlalu Cepat')

@section('description')
    Maaf, sistem menerima terlalu banyak permintaan dari perangkat Anda dalam waktu singkat (rate limit). Silakan tunggu beberapa menit sebelum mencoba mengakses kembali.
@endsection

@section('action')
    <!-- Home button is sufficient -->
@endsection
