@extends('errors.layout')

@section('code', '304')
@section('family', '3xx')
@section('title', 'Tidak Ada Perubahan Data')
@section('heading', 'Konten Belum Berubah')
@section('message', 'Data pada halaman ini masih sama seperti salinan terakhir di perangkat Anda. Silakan muat ulang browser jika ingin mengambil data terbaru.')

@section('extra-action')
    <button type="button" onclick="window.location.reload()" class="btn btn-secondary">Muat Ulang</button>
@endsection
