<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Rekap Listrik {{ $periodeAktif }} | Dusun Demangan</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .paper {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            table {
                font-size: 10px !important;
            }
            th, td {
                padding: 4px 6px !important;
            }
            tr {
                page-break-inside: avoid;
            }
        }

        @page {
            size: A4 landscape;
            margin: 10mm 12mm 12mm 12mm;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-900 antialiased p-3 sm:p-6 min-h-screen">

    <!-- Screen Control Toolbar (Hidden when printing) -->
    <div class="no-print max-w-7xl mx-auto mb-6 bg-white border border-gray-200/90 rounded-2xl p-4 shadow-sm space-y-3">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.rekap-listrik.index', ['periode' => $periodeAktif, 'rt' => $rtParam !== 'semua' ? $rtParam : 1]) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-700 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-xl transition min-h-[40px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Rekap</span>
                </a>
                <div class="h-5 w-px bg-gray-200 hidden sm:block"></div>
                <h1 class="text-sm sm:text-base font-bold text-gray-900">
                    Lembar Rekap Siap Cetak (A4)
                </h1>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button"
                        onclick="window.print()"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-xl shadow-xs transition min-h-[40px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak / Simpan PDF</span>
                </button>

                <a href="{{ route('admin.rekap-listrik.export-csv', ['periode' => $periodeAktif, 'rt' => $rtParam, 'status' => $statusParam]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 rounded-xl shadow-2xs transition min-h-[40px]">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Unduh Excel / CSV</span>
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <form method="GET" action="{{ route('admin.rekap-listrik.cetak') }}" class="pt-3 border-t border-gray-100 flex flex-wrap items-center gap-3 text-xs">
            <!-- Filter Periode -->
            <div class="flex items-center gap-1.5">
                <span class="text-gray-500 font-medium">Periode:</span>
                <select name="periode" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-xs py-1.5 px-2.5 font-bold focus:border-[#CEA042] focus:ring-[#CEA042]">
                    @foreach($daftarPeriode as $p)
                        <option value="{{ $p }}" {{ $p === $periodeAktif ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter RT -->
            <div class="flex items-center gap-1.5">
                <span class="text-gray-500 font-medium">Wilayah:</span>
                <select name="rt" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-xs py-1.5 px-2.5 font-bold focus:border-[#CEA042] focus:ring-[#CEA042]">
                    <option value="semua" {{ $rtParam === 'semua' ? 'selected' : '' }}>Semua RT (RT 01, 02, 03)</option>
                    <option value="1" {{ $rtParam === '1' ? 'selected' : '' }}>RT 01 Saja</option>
                    <option value="2" {{ $rtParam === '2' ? 'selected' : '' }}>RT 02 Saja</option>
                    <option value="3" {{ $rtParam === '3' ? 'selected' : '' }}>RT 03 Saja</option>
                </select>
            </div>

            <!-- Filter Status -->
            <div class="flex items-center gap-1.5">
                <span class="text-gray-500 font-medium">Status:</span>
                <select name="status" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-xs py-1.5 px-2.5 font-bold focus:border-[#CEA042] focus:ring-[#CEA042]">
                    <option value="semua" {{ $statusParam === 'semua' ? 'selected' : '' }}>Semua Status</option>
                    <option value="lunas" {{ $statusParam === 'lunas' ? 'selected' : '' }}>Hanya Lunas</option>
                    <option value="belum_lunas" {{ $statusParam === 'belum_lunas' ? 'selected' : '' }}>Hanya Belum Lunas</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Paper Sheet Preview Container -->
    <div class="paper max-w-7xl mx-auto bg-white border border-gray-200/90 rounded-2xl p-6 sm:p-8 shadow-md text-gray-900">
        
        <!-- Header Dokumen (KOP Resmi) -->
        <div class="flex items-center justify-between pb-4 border-b-2 border-gray-900 gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('logo-kartar-dmg-circle.png') }}"
                     alt="Logo Karang Taruna Demangan"
                     class="w-14 h-14 object-contain rounded-full border border-gray-300">
                <div>
                    <h2 class="text-xs font-bold tracking-widest uppercase text-gray-500">
                        Karang Taruna Dusun Demangan &mdash; RW 01
                    </h2>
                    <h1 class="text-lg sm:text-xl font-extrabold text-gray-900 tracking-tight uppercase leading-snug">
                        Rekapitulasi Pembayaran Listrik Warga
                    </h1>
                    <p class="text-xs text-gray-600 font-medium">
                        Periode: <strong class="text-gray-900">{{ $periodeAktif }}</strong>
                        &bull; Wilayah: <strong class="text-gray-900">{{ $rtParam === 'semua' ? 'Semua RT (RT 01, RT 02, RT 03)' : 'RT ' . str_pad($rtParam, 2, '0', STR_PAD_LEFT) }}</strong>
                        &bull; Status: <strong class="text-gray-900">{{ $statusParam === 'semua' ? 'Semua' : ($statusParam === 'lunas' ? 'Lunas' : 'Belum Lunas') }}</strong>
                    </p>
                </div>
            </div>

            <div class="text-right text-[11px] text-gray-500 shrink-0">
                <div>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
                <div>Lokasi Pelayanan: <strong>Balai RW Demangan</strong></div>
            </div>
        </div>

        <!-- KPI Ringkasan Angka (Summary Box) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2 sm:gap-3 py-3 border-b border-gray-200 text-center my-3 bg-gray-50 rounded-xl p-3">
            <div>
                <div class="text-[10px] text-gray-500 font-bold uppercase">Total Struk</div>
                <div class="text-sm font-extrabold text-gray-900">{{ $totalStruk }}</div>
            </div>
            <div>
                <div class="text-[10px] text-emerald-700 font-bold uppercase">Sudah Lunas</div>
                <div class="text-sm font-extrabold text-emerald-700">{{ $totalLunas }}</div>
            </div>
            <div>
                <div class="text-[10px] text-rose-600 font-bold uppercase">Belum Lunas</div>
                <div class="text-sm font-extrabold text-rose-600">{{ $totalBelumLunas }}</div>
            </div>
            <div>
                <div class="text-[10px] text-gray-500 font-bold uppercase">Tagihan PLN</div>
                <div class="text-xs font-bold text-gray-900 font-mono">Rp {{ number_format($totalTagihanAwal, 0, ',', '.') }}</div>
            </div>
            <div>
                <div class="text-[10px] text-amber-700 font-bold uppercase">Kas KT (@3.000)</div>
                <div class="text-xs font-bold text-amber-700 font-mono">Rp {{ number_format($totalKasKartar, 0, ',', '.') }}</div>
            </div>
            <div>
                <div class="text-[10px] text-gray-900 font-bold uppercase">Grand Total</div>
                <div class="text-xs font-extrabold text-gray-900 font-mono">Rp {{ number_format($grandTotalWajib, 0, ',', '.') }}</div>
            </div>
            <div>
                <div class="text-[10px] text-emerald-800 font-bold uppercase">Terbayar (Kasir)</div>
                <div class="text-xs font-black text-emerald-800 font-mono">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Tabel Data Rekapitulasi -->
        <div class="overflow-x-auto mt-2">
            <table class="w-full text-left text-xs border border-gray-300 divide-y divide-gray-300">
                <thead class="bg-gray-100 text-gray-800 font-bold">
                    <tr>
                        <th class="py-2 px-2 text-center border-r border-gray-300 w-8">No</th>
                        <th class="py-2 px-2 text-center border-r border-gray-300 w-16">Struk #</th>
                        <th class="py-2 px-2.5 border-r border-gray-300">Nama di Struk PLN</th>
                        <th class="py-2 px-2.5 border-r border-gray-300">Nama Warga</th>
                        <th class="py-2 px-2 text-center border-r border-gray-300 w-12">RT</th>
                        <th class="py-2 px-2 text-right border-r border-gray-300">Tagihan PLN</th>
                        <th class="py-2 px-2 text-right border-r border-gray-300">Kas KT</th>
                        <th class="py-2 px-2 text-right border-r border-gray-300">Saldo Lalu</th>
                        <th class="py-2 px-2 text-right border-r border-gray-300 font-extrabold">Wajib Bayar</th>
                        <th class="py-2 px-2 text-center border-r border-gray-300">Status</th>
                        <th class="py-2 px-2 border-r border-gray-300">Tgl Bayar / Pembayar</th>
                        <th class="py-2 px-2 text-center">Ket.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($daftarRekap as $index => $item)
                        <tr class="{{ $loop->even ? 'bg-gray-50/60' : 'bg-white' }}">
                            <td class="py-1.5 px-2 text-center border-r border-gray-200 text-gray-500 font-mono text-[11px]">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-1.5 px-2 text-center border-r border-gray-200 font-mono font-bold text-[11px]">
                                {{ $item->nomor_struk ?: '-' }}
                            </td>
                            <td class="py-1.5 px-2.5 border-r border-gray-200 font-bold text-gray-900">
                                {{ $item->nama_pelanggan ?: '-' }}
                            </td>
                            <td class="py-1.5 px-2.5 border-r border-gray-200 text-gray-700">
                                {{ $item->nama_warga ?: ($item->warga->nama ?? '-') }}
                            </td>
                            <td class="py-1.5 px-2 text-center border-r border-gray-200 font-semibold">
                                {{ str_pad((string)$item->rt, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-1.5 px-2 text-right border-r border-gray-200 font-mono">
                                Rp {{ number_format($item->tagihan_awal, 0, ',', '.') }}
                            </td>
                            <td class="py-1.5 px-2 text-right border-r border-gray-200 font-mono text-gray-600">
                                Rp {{ number_format($item->kas_karang_taruna ?: 3000, 0, ',', '.') }}
                            </td>
                            <td class="py-1.5 px-2 text-right border-r border-gray-200 font-mono text-[11px] {{ $item->saldo_periode_lalu > 0 ? 'text-emerald-700' : ($item->saldo_periode_lalu < 0 ? 'text-rose-600 font-bold' : 'text-gray-400') }}">
                                @if($item->saldo_periode_lalu != 0)
                                    {{ $item->saldo_periode_lalu > 0 ? '-' : '+' }} Rp {{ number_format(abs($item->saldo_periode_lalu), 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-1.5 px-2 text-right border-r border-gray-200 font-mono font-extrabold text-gray-900">
                                Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="py-1.5 px-2 text-center border-r border-gray-200">
                                @if($item->is_lunas)
                                    <span class="inline-block px-1.5 py-0.5 text-[10px] font-bold rounded bg-emerald-100 text-emerald-800">
                                        LUNAS
                                    </span>
                                @else
                                    <span class="inline-block px-1.5 py-0.5 text-[10px] font-bold rounded bg-rose-100 text-rose-800">
                                        BELUM
                                    </span>
                                @endif
                            </td>
                            <td class="py-1.5 px-2 border-r border-gray-200 text-[11px]">
                                @if($item->is_lunas)
                                    <div>{{ $item->tanggal_bayar ? $item->tanggal_bayar->format('d/m/Y') : 'Lunas' }}</div>
                                    @if($item->nama_pembayar)
                                        <div class="text-[10px] text-gray-500">Oleh: {{ $item->nama_pembayar }}</div>
                                    @endif
                                @else
                                    <span class="text-gray-400 italic">-</span>
                                @endif
                            </td>
                            <td class="py-1.5 px-2 text-center text-[10px] font-mono">
                                @if($item->kode_gabungan)
                                    <span class="bg-purple-100 text-purple-800 px-1 py-0.5 rounded font-bold">
                                        {{ $item->kode_gabungan }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="py-6 text-center text-gray-400 italic">
                                Tidak ada data tagihan untuk periode dan kriteria ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <!-- Total Row -->
                <tfoot class="bg-gray-100 font-bold border-t-2 border-gray-400">
                    <tr>
                        <td colspan="5" class="py-2 px-3 text-right border-r border-gray-300 uppercase tracking-wider text-[11px]">
                            Total Keseluruhan ({{ $totalStruk }} Struk):
                        </td>
                        <td class="py-2 px-2 text-right border-r border-gray-300 font-mono text-xs">
                            Rp {{ number_format($totalTagihanAwal, 0, ',', '.') }}
                        </td>
                        <td class="py-2 px-2 text-right border-r border-gray-300 font-mono text-xs">
                            Rp {{ number_format($totalKasKartar, 0, ',', '.') }}
                        </td>
                        <td class="py-2 px-2 text-right border-r border-gray-300 text-gray-500 text-center">-</td>
                        <td class="py-2 px-2 text-right border-r border-gray-300 font-mono text-xs font-black text-gray-900">
                            Rp {{ number_format($grandTotalWajib, 0, ',', '.') }}
                        </td>
                        <td colspan="3" class="py-2 px-2 text-left text-[11px] text-emerald-800">
                            Terbayar: <strong>Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Tanda Tangan Pengesahan (Signatures) -->
        <div class="mt-8 pt-4 grid grid-cols-2 gap-8 text-center text-xs">
            <div>
                <p class="text-gray-600 mb-16">Mengetahui,<br><strong class="text-gray-900">Ketua RT / RW Dusun Demangan</strong></p>
                <p class="font-bold text-gray-900 underline">( .................................................... )</p>
            </div>
            <div>
                <p class="text-gray-600 mb-16">Dusun Demangan, {{ now()->translatedFormat('d F Y') }}<br><strong class="text-gray-900">Bendahara / Petugas Kasir Listrik</strong></p>
                <p class="font-bold text-gray-900 underline">( {{ auth()->user()->name ?? 'Petugas Kasir' }} )</p>
            </div>
        </div>

    </div>

</body>
</html>

