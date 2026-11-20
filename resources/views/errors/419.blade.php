@extends('errors.layout')

@section('code', '419')
@section('family', '4xx')
@section('title', 'Sesi Halaman Kedaluwarsa')
@section('heading', 'Halaman Kedaluwarsa karena Terlalu Lama Diam')
@section('message', 'Token keamanan formulir sudah kedaluwarsa. Silakan muat ulang halaman terlebih dahulu lalu kirim ulang formulir Anda.')

@section('extra-action')
    <button type="button" onclick="window.location.reload()" class="btn btn-secondary">Muat Ulang Halaman</button>
@endsection
