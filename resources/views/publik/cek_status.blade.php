@extends('layouts.publik')

@section('title', 'Cek Status Peminjaman Inventaris Dusun')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6 sm:space-y-8">

    <!-- Header Section -->
    <div class="text-center max-w-xl mx-auto">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mb-2">
            Pelacakan Status Peminjaman
        </h1>
        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
            Ketikkan nomor tiket resi (misal: <span class="font-mono font-bold text-emas-800">PJ-2610-001</span>) atau nomor WhatsApp Anda untuk melihat persetujuan pengurus.
        </p>

        <!-- Search Form -->
        <form method="GET" action="{{ route('peminjaman.cek-status') }}" class="mt-5 flex flex-col sm:flex-row gap-2 max-w-md mx-auto">
            <label for="input-cek-status" class="sr-only">Nomor tiket resi atau nomor WhatsApp</label>
            <input type="text" id="input-cek-status" name="q" value="{{ $kataKunci }}" required
                   placeholder="Nomor tiket resi / Nomor WhatsApp..."
                   class="input-field flex-1">
            <button type="submit" class="btn-primary px-5 shrink-0">
                Lacak
            </button>
        </form>
    </div>

    <!-- Hasil Pelacakan -->
    @if($kataKunci !== '')
        <div class="space-y-4">
            <div class="flex items-center justify-between text-xs text-gray-600">
                <span>Hasil pelacakan untuk: <strong class="text-gray-900">"{{ $kataKunci }}"</strong></span>
                <span class="tabular-money">Ditemukan: <strong class="text-gray-900">{{ $hasil->count() }}</strong> data</span>
            </div>

            @forelse($hasil as $pinjam)
                <div class="card-surface p-5 sm:p-6 transition hover:border-emas-300">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-gray-100">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-base font-extrabold text-gray-900 font-mono tracking-wider">
                                    {{ $pinjam->kode_pinjam }}
                                </span>
                                <span class="text-xs text-gray-500 tabular-money">
                                    ({{ $pinjam->created_at->format('d M Y, H:i') }})
                                </span>
                            </div>
                            <div class="text-xs text-gray-600 mt-1">
                                Peminjam: <strong class="text-gray-900">{{ $pinjam->nama_peminjam }}</strong> ({{ $pinjam->alamat_rt_rw }})
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if($pinjam->status === 'menunggu')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    <span class="w-2 h-2 rounded-full bg-amber-500" aria-hidden="true"></span>
                                    Menunggu Persetujuan
                                </span>
                            @elseif($pinjam->status === 'disetujui')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-600" aria-hidden="true"></span>
                                    Disetujui (Silakan Ambil Barang)
                                </span>
                            @elseif($pinjam->status === 'dikembalikan')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-800 border border-sky-200">
                                    <span class="w-2 h-2 rounded-full bg-sky-600" aria-hidden="true"></span>
                                    Selesai (Barang Sudah Kembali)
                                </span>
                            @elseif($pinjam->status === 'ditolak')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                    <span class="w-2 h-2 rounded-full bg-rose-600" aria-hidden="true"></span>
                                    Permohonan Ditolak
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Informasi Detail -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 text-xs">
                        <div>
                            <span class="text-gray-500 block mb-0.5">Barang yang Dipinjam:</span>
                            <span class="font-bold text-gray-900 text-sm block">
                                {{ $pinjam->barang->nama_barang }}
                            </span>
                            <span class="text-gray-600 tabular-money">Jumlah: {{ $pinjam->jumlah_pinjam }} unit</span>
                        </div>

                        <div>
                            <span class="text-gray-500 block mb-0.5">Jadwal Peminjaman:</span>
                            <div class="text-gray-800 font-medium tabular-money">
                                Pinjam: {{ $pinjam->tanggal_pinjam->format('d/m/Y') }}
                            </div>
                            <div class="text-gray-800 font-medium tabular-money">
                                Rencana Kembali: {{ $pinjam->rencana_tanggal_kembali->format('d/m/Y') }}
                            </div>
                            @if($pinjam->tanggal_dikembalikan)
                                <div class="text-sky-800 font-semibold mt-1 tabular-money">
                                    Dikembalikan: {{ $pinjam->tanggal_dikembalikan->format('d/m/Y') }}
                                </div>
                            @endif
                        </div>

                        <div>
                            <span class="text-gray-500 block mb-0.5">Keperluan Acara:</span>
                            <p class="text-gray-700 italic">"{{ $pinjam->tujuan_peminjaman }}"</p>

                            @if($pinjam->catatan_admin)
                                <div class="mt-2 p-2.5 rounded-xl bg-[#F8F7F4] border border-gray-200 text-gray-700">
                                    <strong class="text-gray-900 block mb-0.5">Catatan Pengurus:</strong>
                                    {{ $pinjam->catatan_admin }}
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($pinjam->status === 'disetujui')
                        <div class="mt-4 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-900">
                            Barang dapat diambil di: <strong>{{ $pinjam->barang->lokasi_penyimpanan ?? 'Gudang Pos RW Demangan' }}</strong>. Harap tunjukkan kode resi ini saat pengambilan.
                        </div>
                    @endif
                </div>
            @empty
                <div class="card-surface p-8 text-center">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="text-sm font-bold text-gray-900 mb-1">Data peminjaman tidak ditemukan</h3>
                    <p class="text-xs text-gray-600">Pastikan nomor resi atau nomor WhatsApp yang dimasukkan sudah sesuai saat pengajuan.</p>
                </div>
            @endforelse
        </div>
    @else
        <!-- Panduan Awal Sebelum Pencarian -->
        <div class="card-surface p-8 text-center max-w-lg mx-auto">
            <svg class="w-12 h-12 text-emas-700 mx-auto mb-3" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <h3 class="text-base font-bold text-gray-900 mb-1">Cek Status Pengajuan Anda</h3>
            <p class="text-xs text-gray-600 leading-relaxed">
                Warga tidak perlu login untuk melihat status pinjaman barang. Cukup masukkan kode resi atau nomor telepon WhatsApp pada kolom di atas.
            </p>
        </div>
    @endif

</div>
@endsection
