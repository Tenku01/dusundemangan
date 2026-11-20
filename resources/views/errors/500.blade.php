@extends('errors.layout')

@section('code', '500')
@section('family', '5xx')
@section('title', 'Kesalahan Server Internal')
@section('heading', 'Terjadi Gangguan Internal pada Sistem')
@section('message', 'Mohon maaf, sistem mengalami kendala saat memproses permintaan Anda. Silakan coba muat ulang beberapa saat lagi atau hubungi pengurus jika masalah berlanjut.')

@section('extra-action')
    <button type="button" onclick="window.location.reload()" class="btn btn-secondary">Coba Lagi</button>
@endsection
