@extends('errors.layout')

@section('code', '504')
@section('family', '5xx')
@section('title', 'Batas Waktu Gateway Habis')
@section('heading', 'Server Terlalu Lama Merespons')
@section('message', 'Permintaan membutuhkan waktu terlalu lama sehingga sambungan dihentikan oleh gateway server. Silakan coba beberapa saat lagi.')

@section('extra-action')
    <button type="button" onclick="window.location.reload()" class="btn btn-secondary">Coba Lagi</button>
@endsection
