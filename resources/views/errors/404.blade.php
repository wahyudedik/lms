@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan (404)')
@section('code', '404')
@section('icon-bg', 'from-blue-500 to-indigo-600')
@section('badge-bg', 'bg-blue-50 text-blue-800')

@section('icon')
    <i class="fas fa-search-minus"></i>
@endsection

@section('message', 'Halaman Tidak Ditemukan')

@section('description')
    Maaf, halaman yang Anda cari tidak ditemukan. Halaman mungkin telah dihapus, dipindahkan, atau Anda salah mengetikkan alamat URL.
@endsection

@section('action')
    <!-- Default layout home button is sufficient -->
@endsection
