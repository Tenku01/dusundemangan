@extends('errors.layout')

@section('code', isset($exception) && method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : '4xx')
@section('family', '4xx')
@section('title', 'Kesalahan Permintaan')
@section('heading', 'Permintaan Tidak Dapat Dipenuhi')
@section('message', 'Terjadi kesalahan pada permintaan dari browser Anda sehingga tidak dapat diproses oleh sistem. Silakan periksa kembali tautan atau hak akses Anda.')
