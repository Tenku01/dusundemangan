@extends('layouts.admin')

@section('title', 'Laporan Resmi Keuangan Kas')

@section('content')
<style>
    .official-doc-sheet {
        font-family: 'Times New Roman', Georgia, Cambria, serif;
        color: #111827;
        line-height: 1.45;
    }

    .official-doc-sheet .tabular-num {
        font-variant-numeric: tabular-nums;
    }

    .official-kop-rule {
        border-top: 3px solid #111827;
        border-bottom: 1px solid #111827;
        height: 4px;
        margin-top: 10px;
        margin-bottom: 18px;
    }

    .word-meta-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10.5pt;
        margin-bottom: 14px;
    }

    .word-meta-table td {
        padding: 2px 4px;
        vertical-align: top;
        border: none;
    }

    .word-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 9.5pt;
        color: #111827;
    }

    .word-table thead {
        display: table-header-group;
    }

    .word-table th,
    .word-table td {
        border: 1px solid #1f2937;
        padding: 6px 8px;
        vertical-align: top;
    }

    .word-table thead th {
        background-color: #f3f4f6;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 8.5pt;
        letter-spacing: 0.03em;
        color: #111827;
        vertical-align: middle;
    }

    .word-table tbody tr {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .word-table tfoot td {
        background-color: #f9fafb;
        font-weight: 700;
    }

    .doc-section-heading {
        font-size: 10.5pt;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        margin-top: 18px;
        margin-bottom: 8px;
        break-after: avoid;
        page-break-after: avoid;
    }

    .signature-block {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 15mm 18mm 18mm 18mm;
        }

        html,
        body {
            background: #ffffff !important;
            color: #111827 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .official-doc-sheet {
            width: 100% !important;
            max-width: none !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .word-table thead th {
            background-color: #f3f4f6 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .word-table tfoot td {
            background-color: #f9fafb !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="space-y-6 print:space-y-0">

    <!-- Header & Print Action (Web Only) -->
    <div class="card-surface p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                Laporan Ringkas Keuangan Kas
            </h1>
            <p class="text-xs text-gray-600 mt-1">
                Pratinjau surat laporan keuangan resmi format A4 siap cetak atau simpan sebagai PDF.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('admin.keuangan.index') }}" class="btn-secondary px-3.5 text-xs">
                Kembali ke Buku Kas
            </a>
            <button type="button" onclick="window.print()" class="btn-primary px-4 text-xs">
                <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / Unduh PDF Resmi</span>
            </button>
        </div>
    </div>

    <!-- Filter Rentang Periode & Jenis Kas (Web Only) -->
    <div class="card-surface p-4 print:hidden">
        <form method="GET" action="{{ route('admin.keuangan.laporan') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div>
                <label for="laporan-kategori-kas" class="block text-xs font-semibold text-gray-700 mb-1">Jenis Kas</label>
                <select id="laporan-kategori-kas" name="kategori_kas" class="select-field">
                    <option value="">Seluruh Jenis Kas</option>
                    @foreach($daftarKategoriKas as $kKas)
                        <option value="{{ $kKas }}" {{ ($kategoriKas ?? '') === $kKas ? 'selected' : '' }}>
                            {{ $kKas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="laporan-bulan-mulai" class="block text-xs font-semibold text-gray-700 mb-1">Bulan Mulai</label>
                <select id="laporan-bulan-mulai" name="bulan_mulai" class="select-field">
                    @foreach($namaBulanIndo as $m => $nmBulan)
                        <option value="{{ $m }}" {{ (int) $bulanMulai === $m ? 'selected' : '' }}>
                            {{ $nmBulan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="laporan-bulan-selesai" class="block text-xs font-semibold text-gray-700 mb-1">Bulan Selesai</label>
                <select id="laporan-bulan-selesai" name="bulan_selesai" class="select-field">
                    @foreach($namaBulanIndo as $m => $nmBulan)
                        <option value="{{ $m }}" {{ (int) $bulanSelesai === $m ? 'selected' : '' }}>
                            {{ $nmBulan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="laporan-tahun" class="block text-xs font-semibold text-gray-700 mb-1">Tahun</label>
                <select id="laporan-tahun" name="tahun" class="select-field">
                    @for($y = (int) date('Y'); $y >= (int) date('Y') - 3; $y--)
                        <option value="{{ $y }}" {{ (int) $tahun === $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <button type="submit" class="btn-primary px-5 text-xs">
                Tampilkan Laporan
            </button>
        </form>
    </div>

    <!-- Lembar Dokumen Resmi A4 (Terbitan Surat / Dokumen Word) -->
    <div class="official-doc-sheet max-w-[210mm] mx-auto bg-white border border-gray-300 shadow-md p-6 sm:p-12 print:border-0 print:shadow-none print:p-0">

        <!-- KOP SURAT RESMI -->
        <div class="flex items-center gap-4">
            <img src="{{ asset('logo-kartar-dmg-circle.png') }}"
                 alt="Logo Karang Taruna Dusun Demangan"
                 width="68" height="68"
                 class="w-16 h-16 sm:w-[68px] sm:h-[68px] object-contain shrink-0">
            <div class="flex-1 text-center pr-0 sm:pr-12">
                <div class="text-base sm:text-lg font-bold uppercase tracking-wider text-gray-900 leading-tight">
                    Karang Taruna Dusun Demangan
                </div>
                <div class="text-sm font-bold uppercase tracking-wide text-gray-900 mt-0.5">
                    Desa Candimulyo
                </div>
                <div class="text-xs text-gray-700 mt-0.5">
                    Sekretariat: Dusun Demangan, Desa Candimulyo
                </div>
            </div>
        </div>
        <div class="official-kop-rule" aria-hidden="true"></div>

        <!-- JUDUL DOKUMEN -->
        <div class="text-center mb-5">
            <h2 class="text-sm sm:text-base font-bold uppercase tracking-wide underline underline-offset-4 text-gray-900">
                Laporan Pertanggungjawaban Keuangan Kas
            </h2>
            <div class="text-xs text-gray-800 mt-1 tabular-num">
                Nomor: LAP-KAS/{{ $tahun }}/{{ str_pad((string) $bulanMulai, 2, '0', STR_PAD_LEFT) }}-{{ str_pad((string) $bulanSelesai, 2, '0', STR_PAD_LEFT) }}
            </div>
        </div>

        <!-- IDENTITAS SURAT (RATA TABEL WORD TANPA GARIS) -->
        <table class="word-meta-table">
            <tbody>
                <tr>
                    <td style="width: 140px; font-weight: 700;">Organisasi</td>
                    <td style="width: 14px;">:</td>
                    <td>Karang Taruna Dusun Demangan, Desa Candimulyo</td>
                </tr>
                <tr>
                    <td style="font-weight: 700;">Perihal</td>
                    <td>:</td>
                    <td>Laporan Arus Kas Keuangan ({{ !empty($kategoriKas) ? $kategoriKas : 'Seluruh Jenis Kas' }})</td>
                </tr>
                <tr>
                    <td style="font-weight: 700;">Periode Laporan</td>
                    <td>:</td>
                    <td>{{ $namaBulanMulai }} s.d. {{ $namaBulanSelesai }} {{ $tahun }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 700;">Tanggal Terbit</td>
                    <td>:</td>
                    <td>{{ $tanggalCetakId }}</td>
                </tr>
            </tbody>
        </table>

        <!-- PARAGRAF PENGANTAR SURAT RESMI -->
        <p class="text-[10.5pt] text-justify mb-4">
            Dengan hormat, bersama surat laporan ini kami sampaikan pertanggungjawaban posisi pemasukan, pengeluaran, serta rincian mutasi transaksi keuangan
            Karang Taruna Dusun Demangan, Desa Candimulyo untuk periode <strong>{{ $namaBulanMulai }}</strong> sampai dengan <strong>{{ $namaBulanSelesai }} {{ $tahun }}</strong>
            ({{ !empty($kategoriKas) ? 'cakupan ' . $kategoriKas : 'cakupan seluruh jenis kas' }}) sebagai bentuk transparansi dan akuntabilitas administrasi organisasi.
        </p>

        <!-- BAGIAN I: IKHTISAR ARUS KAS -->
        <h3 class="doc-section-heading">I. Ikhtisar Arus Kas</h3>
        <div class="overflow-x-auto print:overflow-visible">
            <table class="word-table">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">No.</th>
                        <th style="width: 210px; text-align: left;">Komponen Rekapitulasi</th>
                        <th style="text-align: left;">Keterangan</th>
                        <th style="width: 150px; text-align: right;">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;" class="tabular-num">1</td>
                        <td style="font-weight: 700;">Total Pemasukan Kas</td>
                        <td>Akumulasi penerimaan kas tercatat selama periode berjalan</td>
                        <td style="text-align: right; font-weight: 700;" class="tabular-num">
                            Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center;" class="tabular-num">2</td>
                        <td style="font-weight: 700;">Total Pengeluaran Kas</td>
                        <td>Akumulasi belanja dan pengeluaran kas selama periode berjalan</td>
                        <td style="text-align: right; font-weight: 700;" class="tabular-num">
                            Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right; text-transform: uppercase;">
                            Surplus / (Defisit) Bersih Periode
                        </td>
                        <td style="text-align: right;" class="tabular-num">
                            {{ $surplusDefisit < 0 ? '-' : '' }}Rp {{ number_format(abs($surplusDefisit), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- BAGIAN II: REKAPITULASI PER POS / KATEGORI -->
        <h3 class="doc-section-heading">II. Rincian per Pos Kategori</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 print:grid-cols-2 gap-4">
            <div class="overflow-x-auto print:overflow-visible">
                <div class="text-[9.5pt] font-bold mb-1.5">A. Rincian Pos Pemasukan</div>
                <table class="word-table">
                    <thead>
                        <tr>
                            <th style="width: 36px; text-align: center;">No.</th>
                            <th style="text-align: left;">Pos / Kategori</th>
                            <th style="width: 130px; text-align: right;">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapKategoriMasuk as $idx => $rm)
                            <tr>
                                <td style="text-align: center;" class="tabular-num">{{ $idx + 1 }}</td>
                                <td>{{ $rm->kategori }}</td>
                                <td style="text-align: right;" class="tabular-num">Rp {{ number_format($rm->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; font-style: italic; color: #4b5563;">
                                    Tidak ada catatan pemasukan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" style="text-align: right;">Jumlah Pemasukan</td>
                            <td style="text-align: right;" class="tabular-num">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="overflow-x-auto print:overflow-visible">
                <div class="text-[9.5pt] font-bold mb-1.5">B. Rincian Pos Pengeluaran</div>
                <table class="word-table">
                    <thead>
                        <tr>
                            <th style="width: 36px; text-align: center;">No.</th>
                            <th style="text-align: left;">Pos / Kategori</th>
                            <th style="width: 130px; text-align: right;">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapKategoriKeluar as $idx => $rk)
                            <tr>
                                <td style="text-align: center;" class="tabular-num">{{ $idx + 1 }}</td>
                                <td>{{ $rk->kategori }}</td>
                                <td style="text-align: right;" class="tabular-num">Rp {{ number_format($rk->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; font-style: italic; color: #4b5563;">
                                    Tidak ada catatan pengeluaran pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" style="text-align: right;">Jumlah Pengeluaran</td>
                            <td style="text-align: right;" class="tabular-num">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- BAGIAN III: DAFTAR RINCIAN MUTASI TRANSAKSI -->
        <h3 class="doc-section-heading">
            III. Daftar Rincian Mutasi Transaksi Kas (<span class="tabular-num">{{ $transaksi->count() }}</span> Transaksi)
        </h3>
        <div class="overflow-x-auto print:overflow-visible">
            <table class="word-table">
                <thead>
                    <tr>
                        <th style="width: 34px; text-align: center;">No.</th>
                        <th style="width: 78px; text-align: center;">Tanggal</th>
                        <th style="width: 90px; text-align: left;">Kode</th>
                        <th style="width: 112px; text-align: left;">Jenis Kas</th>
                        <th style="width: 115px; text-align: left;">Pos / Kategori</th>
                        <th style="text-align: left;">Uraian Transaksi</th>
                        <th style="width: 105px; text-align: right;">Pemasukan (Rp)</th>
                        <th style="width: 105px; text-align: right;">Pengeluaran (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $idx => $t)
                        <tr>
                            <td style="text-align: center;" class="tabular-num">{{ $idx + 1 }}</td>
                            <td style="text-align: center; white-space: nowrap;" class="tabular-num">{{ $t->tanggal->format('d/m/Y') }}</td>
                            <td style="white-space: nowrap; font-family: 'Courier New', monospace; font-size: 8.5pt;">{{ $t->kode_transaksi }}</td>
                            <td>{{ $t->labelKategoriKas() }}</td>
                            <td style="font-weight: 600;">{{ $t->kategori }}</td>
                            <td>{{ $t->keterangan }}</td>
                            <td style="text-align: right; white-space: nowrap;" class="tabular-num">
                                {{ $t->jenis === 'masuk' ? 'Rp ' . number_format($t->nominal, 0, ',', '.') : '-' }}
                            </td>
                            <td style="text-align: right; white-space: nowrap;" class="tabular-num">
                                {{ $t->jenis === 'keluar' ? 'Rp ' . number_format($t->nominal, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; font-style: italic; padding: 18px; color: #4b5563;">
                                Tidak ada data transaksi kas pada rentang periode yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" style="text-align: right; text-transform: uppercase;">Jumlah Total Mutasi</td>
                        <td style="text-align: right; white-space: nowrap;" class="tabular-num">
                            Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                        </td>
                        <td style="text-align: right; white-space: nowrap;" class="tabular-num">
                            Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" style="text-align: right; text-transform: uppercase;">Saldo / Selisih Bersih Periode</td>
                        <td colspan="2" style="text-align: right; white-space: nowrap;" class="tabular-num">
                            {{ $surplusDefisit < 0 ? '-' : '' }}Rp {{ number_format(abs($surplusDefisit), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- PENUTUP & TANDA TANGAN KETUA SAJA -->
        <p class="text-[10.5pt] text-justify mt-5">
            Demikian laporan keuangan ini disusun secara objektif sesuai catatan pembukuan organisasi untuk diketahui dan dipergunakan sebagaimana mestinya.
        </p>

        <div class="signature-block flex justify-end mt-8">
            <div class="text-center text-[10.5pt]" style="min-width: 240px;">
                <div>Dusun Demangan, {{ $tanggalCetakId }}</div>
                <div class="mt-0.5">Mengetahui dan Mengesahkan,</div>
                <div class="font-bold">Ketua Karang Taruna Dusun Demangan</div>
                <div style="height: 72px;"></div>
                <div class="font-bold underline underline-offset-4">{{ $namaKetua }}</div>
            </div>
        </div>

    </div>

</div>
@endsection
