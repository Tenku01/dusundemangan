@extends('errors.layout')

@section('code', isset($exception) && method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : '3xx')
@section('family', '3xx')
@section('title', 'Pengalihan Halaman')
@section('heading', 'Permintaan Memerlukan Pengalihan')
@section('message', 'Sistem memerlukan pengalihan ke alamat lain untuk melanjutkan permintaan Anda. Silakan kembali ke beranda untuk melanjutkan.')
