@extends('layouts.publik')

@section('title', 'Permohonan Pinjaman Berhasil | ' . $peminjaman->kode_pinjam)

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl border border-gray-200/90 shadow-sm p-6 sm:p-8 text-center">

        <!-- Brand Organization Badge -->
        <div class="inline-flex items-center gap-2 mb-3 bg-gray-50 border border-gray-200 px-3 py-1 rounded-full">
            <img src="{{ asset('logo-kartar-dmg-circle.png') }}"
                 alt="Logo Dusun Demangan"
                 class="w-6 h-6 rounded-full object-cover">
            <span class="text-[11px] font-bold text-gray-700 tracking-wide">Dusun Demangan</span>
        </div>

        <!-- Success Icon -->
        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">Permohonan Pinjaman Terkirim!</h1>
        <p class="text-sm text-gray-600 mb-6">
            Permohonan peminjaman inventaris Anda telah tercatat di sistem dusun dan sedang menunggu konfirmasi pengurus.
        </p>

        <!-- Kode Resi Card -->
        <div class="bg-amber-50/80 border border-amber-200/90 rounded-xl p-5 mb-6 text-center">
            <div class="text-xs font-semibold text-[#9B6C26] uppercase tracking-wider mb-1">
                Kode Resi / Nomor Tiket Anda
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-[#CEA042] font-mono tracking-wider mb-2">
                {{ $peminjaman->kode_pinjam }}
            </div>
            <p class="text-xs text-gray-500">
                Simpan nomor tiket ini untuk melacak status persetujuan barang.
            </p>
        </div>

        <!-- Detail Rincian -->
        <div class="bg-gray-50 rounded-xl p-4 text-left text-xs space-y-2.5 mb-6 border border-gray-200/60">
            <div class="flex justify-between py-1 border-b border-gray-200/60">
                <span class="text-gray-500">Barang yang Dipinjam:</span>
                <span class="font-bold text-gray-800 text-right">{{ $peminjaman->barang->nama_barang }} ({{ $peminjaman->jumlah_pinjam }} unit)</span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-200/60">
                <span class="text-gray-500">Nama Peminjam:</span>
                <span class="font-semibold text-gray-800">{{ $peminjaman->nama_peminjam }} ({{ $peminjaman->alamat_rt_rw }})</span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-200/60">
                <span class="text-gray-500">Nomor Kontak / WhatsApp:</span>
                <span class="font-semibold text-gray-800">{{ $peminjaman->kontak_peminjam }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-200/60">
                <span class="text-gray-500">Tanggal Pinjam:</span>
                <span class="font-semibold text-gray-800">{{ $peminjaman->tanggal_pinjam->format('d F Y') }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-200/60">
                <span class="text-gray-500">Rencana Pengembalian:</span>
                <span class="font-semibold text-gray-800">{{ $peminjaman->rencana_tanggal_kembali->format('d F Y') }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-200/60">
                <span class="text-gray-500">Keperluan:</span>
                <span class="font-semibold text-gray-800 text-right max-w-xs">{{ $peminjaman->tujuan_peminjaman }}</span>
            </div>
            <div class="flex justify-between py-1">
                <span class="text-gray-500">Status Saat Ini:</span>
                <span class="inline-flex items-center gap-1 font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    Menunggu Persetujuan
                </span>
            </div>
        </div>

        <!-- Tombol Aksi WhatsApp Pengurus -->
        <div class="space-y-3">
            <a href="https://wa.me/?text={{ $pesanWa }}" target="_blank"
               class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                Kirim Konfirmasi ke WhatsApp Pengurus
            </a>

            <div class="flex items-center justify-center gap-3 pt-2">
                <a href="{{ route('peminjaman.cek-status') }}?q={{ $peminjaman->kode_pinjam }}"
                   class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                    Lacak Status Pinjaman
                </a>
                <a href="{{ route('beranda') }}"
                   class="px-4 py-2 text-xs font-semibold text-[#CEA042] hover:underline">
                    Kembali ke Beranda
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
