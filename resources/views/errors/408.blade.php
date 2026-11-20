@extends('errors.layout')

@section('code', '408')
@section('family', '4xx')
@section('title', 'Waktu Permintaan Habis')
@section('heading', 'Koneksi Permintaan Melebihi Batas Waktu')
@section('message', 'Server menunggu terlalu lama untuk menerima permintaan lengkap dari perangkat Anda. Pastikan koneksi internet stabil lalu coba muat ulang.')

@section('extra-action')
    <button type="button" onclick="window.location.reload()" class="btn btn-secondary">Muat Ulang</button>
@endsection
