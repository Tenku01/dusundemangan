@extends('layouts.admin')

@section('title', 'Status Sistem, Log Perubahan & Update Web')

@section('content')
<div class="space-y-5">

    <!-- Header & Ringkasan Status -->
    <div class="card-surface p-4 sm:p-5 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-900 text-emas-400 font-bold shrink-0 shadow-sm">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </span>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-lg sm:text-2xl font-bold text-gray-900 tracking-tight leading-tight">
                            Pusat Status &amp; Audit Sistem
                        </h1>
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emas-100 text-emas-900 border border-emas-300 tabular-money">
                            {{ $currentVersion }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-600 mt-0.5">
                        Pantau log perubahan data pengurus, status method &amp; memori server, serta riwayat pembaruan web permanen.
                    </p>
                </div>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="btn-secondary px-3.5 text-xs self-start sm:self-auto shrink-0">
                Kembali ke Dashboard
            </a>
        </div>

        <!-- 3 KPI Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="rounded-xl border border-gray-200/90 bg-[#F8F7F4] p-3.5">
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500">1. Log Perubahan Data</div>
                <div class="text-xl font-extrabold text-gray-900 mt-1 tabular-money">{{ number_format($totalDataLogs, 0, ',', '.') }} Catatan</div>
                <div class="text-[11px] text-gray-600 mt-0.5">Audit otomatis pelaku ubah/tambah/hapus data</div>
            </div>

            <div class="rounded-xl border border-gray-200/90 bg-[#F8F7F4] p-3.5">
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500">2. Status Method &amp; Memori</div>
                <div class="text-xl font-extrabold text-emas-800 mt-1 tabular-money">
                    {{ number_format($memoryUsageBytes / 1048576, 2) }} MB
                    <span class="text-xs font-semibold text-gray-500">/ {{ $memoryLimit }}</span>
                </div>
                <div class="text-[11px] text-gray-600 mt-0.5 tabular-money">
                    {{ number_format($totalMethodLogs, 0, ',', '.') }} log method &bull; {{ $registeredMethods->count() }} route aktif
                </div>
            </div>

            <div class="rounded-xl border border-gray-200/90 bg-[#F8F7F4] p-3.5">
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500">3. Status Update Web</div>
                <div class="text-xl font-extrabold text-emerald-700 mt-1 tabular-money">{{ $currentVersion }}</div>
                <div class="text-[11px] text-gray-600 mt-0.5">Riwayat rilis permanen (tidak dapat dihapus)</div>
            </div>
        </div>
    </div>

    <!-- 3 Tab Navigasi Utama -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 bg-gray-200/80 p-1.5 rounded-2xl">
        <a href="{{ route('status.index', ['tab' => 'perubahan']) }}"
           class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl text-xs font-bold transition min-h-[46px] {{ $tab === 'perubahan' ? 'bg-[#CEA042] text-[#1F1406] shadow-sm border border-[#BA8832]/40' : 'bg-white text-gray-700 hover:bg-emas-50' }}">
            <svg class="w-4 h-4 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>Log Perubahan Data</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] tabular-money {{ $tab === 'perubahan' ? 'bg-[#1F1406]/15 text-[#1F1406]' : 'bg-gray-100 text-gray-700' }}">
                {{ $totalDataLogs }}
            </span>
        </a>

        <a href="{{ route('status.index', ['tab' => 'method']) }}"
           class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl text-xs font-bold transition min-h-[46px] {{ $tab === 'method' ? 'bg-[#CEA042] text-[#1F1406] shadow-sm border border-[#BA8832]/40' : 'bg-white text-gray-700 hover:bg-emas-50' }}">
            <svg class="w-4 h-4 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span>Status Method &amp; Memori</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] tabular-money {{ $tab === 'method' ? 'bg-[#1F1406]/15 text-[#1F1406]' : 'bg-gray-100 text-gray-700' }}">
                {{ $totalMethodLogs }}
            </span>
        </a>

        <a href="{{ route('status.index', ['tab' => 'update']) }}"
           class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl text-xs font-bold transition min-h-[46px] {{ $tab === 'update' ? 'bg-[#CEA042] text-[#1F1406] shadow-sm border border-[#BA8832]/40' : 'bg-white text-gray-700 hover:bg-emas-50' }}">
            <svg class="w-4 h-4 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span>Status Update Web</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] tabular-money {{ $tab === 'update' ? 'bg-[#1F1406]/15 text-[#1F1406]' : 'bg-emerald-100 text-emerald-800' }}">
                {{ count($webUpdates) }}
            </span>
        </a>
    </div>

    <!-- ======================================================================
         TAB 1 (PRIORITAS): LOG PERUBAHAN DATA (SIAPA YANG MENGUBAH DATA)
         ====================================================================== -->
    @if($tab === 'perubahan')
        <div class="space-y-4">
            @if(! $dataTableExists)
                <div role="alert" class="rounded-2xl bg-amber-50 border border-amber-300 p-4 text-xs text-amber-950">
                    Tabel <code class="font-mono font-bold">data_change_logs</code> belum tersedia. Jalankan <code class="font-mono font-bold">php artisan migrate</code> agar log perubahan data mulai merekam otomatis.
                </div>
            @endif

            <!-- Filter & Tombol Bersihkan Log Perubahan -->
            <div class="card-surface p-4 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gray-100">
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-gray-900">Audit Trail: Siapa yang Mengubah Data</h2>
                        <p class="text-xs text-gray-600">Merekam pelaku, waktu, jenis data, serta nilai sebelum &amp; sesudah perubahan.</p>
                    </div>

                    <form method="POST" action="{{ route('status.clear-data-logs') }}"
                          onsubmit="return confirm('Bersihkan seluruh log perubahan data sekarang? Tindakan ini tidak mengubah data warga/kas/inventaris.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger-soft text-xs shrink-0" {{ $totalDataLogs === 0 ? 'disabled' : '' }}>
                            <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Bersihkan Log Perubahan ({{ number_format($totalDataLogs, 0, ',', '.') }})</span>
                        </button>
                    </form>
                </div>

                <form method="GET" action="{{ route('status.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                    <input type="hidden" name="tab" value="perubahan">

                    <div>
                        <label for="filter-cari-log" class="block text-xs font-semibold text-gray-700 mb-1">Cari Pelaku / Data</label>
                        <input id="filter-cari-log" type="text" name="cari" value="{{ $cariFilter }}"
                               placeholder="Nama admin, email, atau objek..." class="input-field">
                    </div>

                    <div>
                        <label for="filter-aksi-log" class="block text-xs font-semibold text-gray-700 mb-1">Jenis Aksi</label>
                        <select id="filter-aksi-log" name="aksi" onchange="this.form.submit()" class="select-field">
                            <option value="">Semua Aksi</option>
                            <option value="created" {{ $aksiFilter === 'created' ? 'selected' : '' }}>Tambah Data</option>
                            <option value="updated" {{ $aksiFilter === 'updated' ? 'selected' : '' }}>Ubah Data</option>
                            <option value="deleted" {{ $aksiFilter === 'deleted' ? 'selected' : '' }}>Hapus Data</option>
                        </select>
                    </div>

                    <div>
                        <label for="filter-model-log" class="block text-xs font-semibold text-gray-700 mb-1">Modul Data</label>
                        <select id="filter-model-log" name="model" onchange="this.form.submit()" class="select-field">
                            <option value="">Semua Modul</option>
                            @foreach($daftarModelLabel as $mLabel)
                                <option value="{{ $mLabel }}" {{ $modelFilter === $mLabel ? 'selected' : '' }}>{{ $mLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="btn-primary flex-1 text-xs">Terapkan Filter</button>
                        @if($cariFilter || $aksiFilter || $modelFilter)
                            <a href="{{ route('status.index', ['tab' => 'perubahan']) }}" class="btn-subtle text-xs shrink-0">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Mobile Card View Log Perubahan -->
            <div class="block md:hidden space-y-3">
                @forelse($dataLogs as $log)
                    <div class="card-surface p-4 space-y-2.5">
                        <div class="flex items-center justify-between gap-2 pb-2 border-b border-gray-100">
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold {{ $log->badgeClassesAksi() }}">
                                {{ $log->labelAksi() }}
                            </span>
                            <span class="text-[11px] text-gray-500 tabular-money">{{ $log->created_at?->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <div class="text-xs font-extrabold text-gray-900">{{ $log->user_name }}</div>
                                <div class="text-[11px] text-gray-500">{{ ucfirst($log->user_peran) }} &bull; {{ $log->ip_address ?? '-' }}</div>
                            </div>
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                {{ $log->model_label }}
                            </span>
                        </div>
                        <div class="text-xs font-semibold text-emas-900 bg-emas-50/70 px-2.5 py-1.5 rounded-lg border border-emas-200/80">
                            {{ $log->subject_label ?? ('ID #' . $log->model_id) }}
                        </div>
                        @if($log->aksi === 'updated' && !empty($log->new_values))
                            <div class="text-[11px] space-y-1 bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                                @foreach($log->new_values as $field => $newVal)
                                    @php $oldVal = $log->old_values[$field] ?? null; @endphp
                                    <div>
                                        <span class="font-mono font-bold text-gray-700">{{ $field }}:</span>
                                        <span class="text-rose-700 line-through">{{ is_scalar($oldVal) ? $oldVal : json_encode($oldVal) }}</span>
                                        <span class="text-gray-400">&rarr;</span>
                                        <span class="text-emerald-800 font-semibold">{{ is_scalar($newVal) ? $newVal : json_encode($newVal) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="card-surface p-8 text-center text-xs text-gray-500">
                        Belum ada catatan log perubahan data.
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table Log Perubahan -->
            <div class="hidden md:block card-surface overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                        <thead class="bg-[#F8F7F4] text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="py-3.5 px-4">Waktu</th>
                                <th class="py-3.5 px-4">Pelaku (Siapa yang Mengubah)</th>
                                <th class="py-3.5 px-4">Aksi</th>
                                <th class="py-3.5 px-4">Modul &amp; Subjek Data</th>
                                <th class="py-3.5 px-4">Detail Perubahan Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($dataLogs as $log)
                                <tr class="hover:bg-emas-50/40 transition align-top">
                                    <td class="py-3.5 px-4 whitespace-nowrap text-gray-700 tabular-money">
                                        <div class="font-semibold">{{ $log->created_at?->format('d/m/Y') }}</div>
                                        <div class="text-[11px] text-gray-500">{{ $log->created_at?->format('H:i:s') }} WIB</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-gray-900">{{ $log->user_name }}</div>
                                        <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $log->user_peran === 'superadmin' ? 'bg-slate-900 text-emas-300' : ($log->user_peran === 'admin' ? 'bg-emas-100 text-emas-900' : 'bg-gray-100 text-gray-600') }}">
                                                {{ $log->user_peran }}
                                            </span>
                                            @if($log->ip_address)
                                                <span class="text-[11px] font-mono text-gray-500">{{ $log->ip_address }}</span>
                                            @endif
                                        </div>
                                        @if($log->user_email)
                                            <div class="text-[11px] text-gray-500 mt-0.5">{{ $log->user_email }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-bold {{ $log->badgeClassesAksi() }}">
                                            {{ $log->labelAksi() }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                            {{ $log->model_label }}
                                        </span>
                                        <div class="font-semibold text-gray-900 mt-1">{{ $log->subject_label ?? ('ID #' . $log->model_id) }}</div>
                                        <div class="text-[11px] text-gray-400 font-mono">ID #{{ $log->model_id ?? '-' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 max-w-md">
                                        @if($log->aksi === 'updated' && !empty($log->new_values))
                                            <div class="space-y-1">
                                                @foreach($log->new_values as $field => $newVal)
                                                    @php $oldVal = $log->old_values[$field] ?? null; @endphp
                                                    <div class="text-[11px] bg-gray-50 rounded-lg px-2 py-1 border border-gray-100">
                                                        <span class="font-mono font-bold text-gray-800">{{ $field }}</span>:
                                                        <span class="text-rose-700 line-through break-all">{{ is_scalar($oldVal) || is_null($oldVal) ? ($oldVal ?? 'kosong') : json_encode($oldVal) }}</span>
                                                        <span class="text-gray-400 mx-0.5">&rarr;</span>
                                                        <span class="text-emerald-800 font-semibold break-all">{{ is_scalar($newVal) || is_null($newVal) ? ($newVal ?? 'kosong') : json_encode($newVal) }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif($log->aksi === 'created' && !empty($log->new_values))
                                            <div class="text-[11px] text-emerald-800 bg-emerald-50/60 border border-emerald-200/70 rounded-lg px-2.5 py-1.5">
                                                Menambahkan record baru dengan {{ count($log->new_values) }} kolom data.
                                            </div>
                                        @elseif($log->aksi === 'deleted')
                                            <div class="text-[11px] text-rose-800 bg-rose-50/70 border border-rose-200/70 rounded-lg px-2.5 py-1.5">
                                                Data dihapus permanen oleh {{ $log->user_name }}.
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-500">
                                        Belum ada catatan log perubahan data. Setiap penambahan, pengubahan, atau penghapusan data akan otomatis muncul di sini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($dataLogs->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $dataLogs->links() }}
                    </div>
                @endif
            </div>

            <div class="md:hidden">
                {{ $dataLogs->links() }}
            </div>
        </div>
    @endif

    <!-- ======================================================================
         TAB 2: STATUS METHOD & PEMBERSIHAN MEMORI
         ====================================================================== -->
    @if($tab === 'method')
        <div class="space-y-4">
            @if(! $methodTableExists)
                <div role="alert" class="rounded-2xl bg-amber-50 border border-amber-300 p-4 text-xs text-amber-950">
                    Tabel <code class="font-mono font-bold">method_status_logs</code> belum tersedia. Jalankan <code class="font-mono font-bold">php artisan migrate</code> agar pemantauan eksekusi method mulai tercatat.
                </div>
            @endif

            <!-- Kartu Memori & Tombol Pembersihan -->
            <div class="card-surface p-4 sm:p-5 space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-gray-100">
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-gray-900">Pemantauan Status Method &amp; Memori</h2>
                        <p class="text-xs text-gray-600">Pantau beban method HTTP, waktu eksekusi, serta bersihkan log &amp; cache untuk memperluas memori.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <form method="POST" action="{{ route('status.clear-method-logs') }}"
                              onsubmit="return confirm('Bersihkan seluruh log eksekusi method di database?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger-soft text-xs" {{ $totalMethodLogs === 0 ? 'disabled' : '' }}>
                                <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Bersihkan Log Method ({{ number_format($totalMethodLogs, 0, ',', '.') }})</span>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('status.clear-memory') }}"
                              onsubmit="return confirm('Bersihkan log method, cache aplikasi, dan compiled views untuk memperluas memori sekarang?')">
                            @csrf
                            <button type="submit" class="btn-primary text-xs">
                                <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Bersihkan Memori &amp; Cache</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Metrik Memori & Database -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="rounded-xl border border-gray-200 bg-[#F8F7F4] p-3">
                        <div class="text-[11px] font-bold uppercase text-gray-500">Memori PHP Aktif</div>
                        <div class="text-lg font-extrabold text-gray-900 mt-0.5 tabular-money">{{ number_format($memoryUsageBytes / 1048576, 2) }} MB</div>
                        <div class="text-[11px] text-gray-500">Batas: {{ $memoryLimit }}</div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-[#F8F7F4] p-3">
                        <div class="text-[11px] font-bold uppercase text-gray-500">Puncak Memori (Peak)</div>
                        <div class="text-lg font-extrabold text-emas-800 mt-0.5 tabular-money">{{ number_format($memoryPeakBytes / 1048576, 2) }} MB</div>
                        <div class="text-[11px] text-gray-500">Request siklus saat ini</div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-[#F8F7F4] p-3">
                        <div class="text-[11px] font-bold uppercase text-gray-500">Total Log Method</div>
                        <div class="text-lg font-extrabold text-gray-900 mt-0.5 tabular-money">{{ number_format($totalMethodLogs, 0, ',', '.') }}</div>
                        <div class="text-[11px] text-gray-500">Tersimpan di database</div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-[#F8F7F4] p-3">
                        <div class="text-[11px] font-bold uppercase text-gray-500">Endpoint Terdaftar</div>
                        <div class="text-lg font-extrabold text-emerald-700 mt-0.5 tabular-money">{{ $registeredMethods->count() }}</div>
                        <div class="text-[11px] text-gray-500">Route aktif aplikasi</div>
                    </div>
                </div>

                <!-- Ringkasan per HTTP Method -->
                @if($methodSummaryByVerb->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 pt-2">
                        @foreach($methodSummaryByVerb as $verb)
                            <div class="rounded-xl border border-gray-200 p-3 bg-white">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono text-xs font-extrabold text-gray-900">{{ $verb->http_method }}</span>
                                    <span class="text-[11px] text-gray-500 tabular-money">{{ $verb->avg_ms }} ms</span>
                                </div>
                                <div class="text-base font-extrabold text-emas-800 mt-1 tabular-money">{{ number_format($verb->total_hit, 0, ',', '.') }} hit</div>
                                <div class="text-[11px] {{ (int) $verb->total_error > 0 ? 'text-rose-700 font-semibold' : 'text-emerald-700' }} tabular-money">
                                    {{ (int) $verb->total_error }} error (4xx/5xx)
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Tabel Statistik per Controller@Method -->
            @if($methodStatsByRoute->isNotEmpty())
                <div class="card-surface overflow-hidden">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900">Status Performa Controller &amp; Method Teratas</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                            <thead class="bg-[#F8F7F4] text-gray-600 font-bold uppercase text-[11px]">
                                <tr>
                                    <th class="py-3 px-4">Method</th>
                                    <th class="py-3 px-4">Route / Controller Action</th>
                                    <th class="py-3 px-4 text-right">Total Hit</th>
                                    <th class="py-3 px-4 text-right">Rata-rata</th>
                                    <th class="py-3 px-4 text-center">Status Terakhir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($methodStatsByRoute as $stat)
                                    <tr class="hover:bg-emas-50/40">
                                        <td class="py-3 px-4 whitespace-nowrap font-mono font-bold text-gray-900">{{ $stat->http_method }}</td>
                                        <td class="py-3 px-4">
                                            <div class="font-semibold text-gray-900 font-mono text-[11px]">{{ $stat->route_name ?: '-' }}</div>
                                            <div class="text-[11px] text-gray-500 font-mono break-all">{{ class_basename($stat->controller_action) }}</div>
                                        </td>
                                        <td class="py-3 px-4 text-right font-bold tabular-money">{{ number_format($stat->total_hit, 0, ',', '.') }}</td>
                                        <td class="py-3 px-4 text-right tabular-money text-gray-700">{{ $stat->avg_ms }} ms</td>
                                        <td class="py-3 px-4 text-center tabular-money">
                                            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold {{ (int) $stat->last_status >= 400 ? 'bg-rose-50 text-rose-800 border border-rose-200' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' }}">
                                                HTTP {{ $stat->last_status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Riwayat Eksekusi Method Terbaru -->
            <div class="card-surface overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900">Riwayat Eksekusi Request Terbaru</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                        <thead class="bg-[#F8F7F4] text-gray-600 font-bold uppercase text-[11px]">
                            <tr>
                                <th class="py-3 px-4">Waktu</th>
                                <th class="py-3 px-4">HTTP</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Path &amp; Controller</th>
                                <th class="py-3 px-4">Pengguna</th>
                                <th class="py-3 px-4 text-right">Durasi / Memori</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($methodLogs as $mLog)
                                <tr class="hover:bg-emas-50/40">
                                    <td class="py-3 px-4 whitespace-nowrap text-gray-600 tabular-money">
                                        {{ $mLog->created_at?->format('d/m H:i:s') }}
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-mono font-bold {{ $mLog->badgeClassesHttpMethod() }}">
                                            {{ $mLog->http_method }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap tabular-money">
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-bold {{ $mLog->badgeClassesStatusCode() }}">
                                            {{ $mLog->status_code }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-mono font-semibold text-gray-900">{{ $mLog->path }}</div>
                                        <div class="text-[11px] text-gray-500 font-mono">{{ class_basename((string) $mLog->controller_action) }}</div>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap text-gray-700">
                                        {{ $mLog->user_name ?? 'Publik / Tamu' }}
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap text-right tabular-money text-gray-700">
                                        <span class="font-bold">{{ $mLog->duration_ms }} ms</span>
                                        <span class="text-gray-400">&bull;</span>
                                        <span>{{ $mLog->memory_kb }} KB</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-gray-500">
                                        Belum ada riwayat eksekusi method tersimpan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($methodLogs->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $methodLogs->links() }}
                    </div>
                @endif
            </div>

            <!-- Inventaris Method & Route Terdaftar -->
            <div class="card-surface overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between gap-2">
                    <h3 class="text-sm font-bold text-gray-900">Daftar Method &amp; Endpoint Terdaftar di Sistem</h3>
                    <span class="text-xs font-bold text-emas-800 tabular-money">{{ $registeredMethods->count() }} Endpoint</span>
                </div>
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                        <thead class="bg-[#F8F7F4] text-gray-600 font-bold uppercase text-[11px] sticky top-0">
                            <tr>
                                <th class="py-2.5 px-4">Method</th>
                                <th class="py-2.5 px-4">URI</th>
                                <th class="py-2.5 px-4">Nama Route</th>
                                <th class="py-2.5 px-4">Controller Method</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($registeredMethods as $ep)
                                <tr class="hover:bg-emas-50/30">
                                    <td class="py-2 px-4 whitespace-nowrap font-mono font-bold text-gray-800">
                                        {{ implode(' | ', $ep['methods']) }}
                                    </td>
                                    <td class="py-2 px-4 font-mono text-gray-900">{{ $ep['uri'] }}</td>
                                    <td class="py-2 px-4 font-mono text-[11px] text-emas-800">{{ $ep['name'] }}</td>
                                    <td class="py-2 px-4 font-mono text-[11px] text-gray-600">{{ $ep['action'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ======================================================================
         TAB 3: STATUS UPDATE WEB (PERMANEN / TIDAK DAPAT DIHAPUS)
         ====================================================================== -->
    @if($tab === 'update')
        <div class="space-y-4">
            <div class="card-surface p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-sm sm:text-base font-bold text-gray-900">Riwayat Pembaruan Aplikasi Web</h2>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-900 text-emas-300">
                            <svg class="w-3 h-3" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Terkunci (Permanen &amp; Tidak Dapat Dihapus)
                        </span>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">
                        Catatan rilis dan pembaruan resmi sistem Dusun Demangan tersimpan permanen di konfigurasi sistem.
                    </p>
                </div>

                <div class="text-left sm:text-right shrink-0">
                    <div class="text-[11px] font-bold uppercase text-gray-500">Versi Aktif</div>
                    <div class="text-lg font-extrabold text-emas-800 tabular-money">{{ $currentVersion }}</div>
                </div>
            </div>

            <div class="space-y-3.5">
                @foreach($webUpdates as $idx => $release)
                    <div class="card-surface p-4 sm:p-5 {{ $idx === 0 ? 'border-l-4 border-l-[#CEA042]' : '' }}">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-gray-100">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-extrabold tabular-money {{ $idx === 0 ? 'bg-[#CEA042] text-[#1F1406]' : 'bg-slate-100 text-slate-800 border border-slate-200' }}">
                                    {{ $release['version'] }}
                                </span>
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emas-50 text-emas-900 border border-emas-200">
                                    {{ $release['kategori'] }}
                                </span>
                                @if($idx === 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Rilis Terbaru</span>
                                @endif
                            </div>
                            <span class="text-xs font-semibold text-gray-500 tabular-money">{{ $release['tanggal'] }}</span>
                        </div>

                        <h3 class="text-sm sm:text-base font-bold text-gray-900 mt-3">
                            {{ $release['judul'] }}
                        </h3>
                        <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                            {{ $release['ringkasan'] }}
                        </p>

                        @if(!empty($release['poin']))
                            <ul class="mt-3 space-y-1.5 text-xs text-gray-700">
                                @foreach($release['poin'] as $poin)
                                    <li class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>{{ $poin }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
