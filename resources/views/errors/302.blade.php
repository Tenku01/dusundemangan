@extends('errors.layout')

@section('code', '302')
@section('family', '3xx')
@section('title', 'Pengalihan Sementara')
@section('heading', 'Halaman Dialihkan Sementara')
@section('message', 'Permintaan Anda sedang dialihkan sementara ke rute lain. Jika pengalihan tidak berjalan otomatis, silakan muat ulang atau kembali ke beranda.')

@section('extra-action')
    <button type="button" onclick="window.location.reload()" class="btn btn-secondary">Muat Ulang</button>
@endsection
