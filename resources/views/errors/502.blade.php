@extends('errors.layout')

@section('code', '502')
@section('family', '5xx')
@section('title', 'Gateway Tidak Valid')
@section('heading', 'Server Penghubung Menerima Respons Tidak Valid')
@section('message', 'Jaringan penghubung antar layanan sedang mengalami gangguan sementara. Silakan tunggu sebentar dan muat ulang halaman ini.')

@section('extra-action')
    <button type="button" onclick="window.location.reload()" class="btn btn-secondary">Muat Ulang</button>
@endsection
