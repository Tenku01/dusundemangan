@extends('errors.layout')

@section('code', '403')
@section('family', '4xx')
@section('title', 'Akses Ditolak')
@section('heading', 'Anda Tidak Memiliki Hak Akses')
@section('message', 'Maaf, akun Anda tidak memiliki izin untuk membuka halaman atau menjalankan aksi ini. Hubungi Superadmin jika menurut Anda ini adalah kekeliruan.')

@section('extra-action')
    @if(\Illuminate\Support\Facades\Route::has('login'))
        <a href="{{ route('login') }}" class="btn btn-secondary">Halaman Login</a>
    @endif
@endsection
