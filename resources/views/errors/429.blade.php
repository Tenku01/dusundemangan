@extends('errors.layout')

@section('code', '429')
@section('family', '4xx')
@section('title', 'Terlalu Banyak Permintaan')
@section('heading', 'Permintaan Terlalu Cepat dalam Waktu Singkat')
@section('message', 'Sistem membatasi frekuensi akses sementara untuk menjaga kestabilan server. Tunggu beberapa saat sebelum mencoba lagi.')
