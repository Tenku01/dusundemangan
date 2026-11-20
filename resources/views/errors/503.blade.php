@extends('errors.layout')

@section('code', '503')
@section('family', '5xx')
@section('title', 'Layanan Sedang Pemeliharaan')
@section('heading', 'Sistem Sedang Tidak Tersedia Sementara')
@section('message', 'Portal Karang Taruna Dusun Demangan sedang dalam pemeliharaan atau mengalami beban tinggi sementara. Silakan kembali beberapa menit lagi.')

@section('extra-action')
    <button type="button" onclick="window.location.reload()" class="btn btn-secondary">Muat Ulang</button>
@endsection
