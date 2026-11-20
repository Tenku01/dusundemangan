@extends('errors.layout')

@section('code', isset($exception) && method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : '5xx')
@section('family', '5xx')
@section('title', 'Gangguan Server')
@section('heading', 'Terjadi Kesalahan pada Layanan Server')
@section('message', 'Mohon maaf, layanan server sedang mengalami gangguan sementara. Silakan muat ulang halaman beberapa saat lagi.')

@section('extra-action')
    <button type="button" onclick="window.location.reload()" class="btn btn-secondary">Coba Lagi</button>
@endsection
