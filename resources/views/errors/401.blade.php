@extends('errors.layout')

@section('code', '401')
@section('family', '4xx')
@section('title', 'Belum Terautentikasi')
@section('heading', 'Anda Perlu Masuk Terlebih Dahulu')
@section('message', 'Halaman ini memerlukan autentikasi akun pengurus. Silakan login terlebih dahulu sebelum melanjutkan.')

@section('extra-action')
    @if(\Illuminate\Support\Facades\Route::has('login'))
        <a href="{{ route('login') }}" class="btn btn-secondary">Masuk Pengurus</a>
    @endif
@endsection
