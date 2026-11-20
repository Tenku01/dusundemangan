@props(['triggerButtonId' => 'btn-install-pwa-publik', 'triggerDisplayClass' => 'inline-flex'])

<!-- Banner Tip Instalasi Khusus iPhone / iPad (Non-Standalone) -->
<div id="ios-pwa-banner"
     class="hidden fixed bottom-20 lg:bottom-4 left-3 right-3 sm:left-auto sm:right-5 sm:w-96 z-40 bg-white border border-emas-300 rounded-2xl shadow-xl p-3.5 items-center justify-between gap-3">
    <div class="flex items-center gap-3 min-w-0">
        <img src="{{ asset('icon-apple-touch.png') }}"
             alt="Ikon Aplikasi Dusun Demangan"
             width="40" height="40"
             class="w-10 h-10 rounded-xl border border-emas-200 shadow-sm shrink-0 bg-white">
        <div class="min-w-0">
            <div class="text-xs font-bold text-slate-900 truncate">Pasang di Layar Utama iPhone</div>
            <div class="text-[11px] text-slate-600 leading-snug">Buka cepat seperti aplikasi tanpa lewat browser</div>
        </div>
    </div>
    <div class="flex items-center gap-1 shrink-0">
        <button type="button"
                onclick="openIosInstallModal()"
                class="btn-primary px-3 py-1.5 text-xs min-h-[38px]">
            Pasang
        </button>
        <button type="button"
                onclick="dismissIosPwaBanner()"
                aria-label="Tutup banner instalasi iPhone"
                class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg min-h-[38px] min-w-[38px] flex items-center justify-center transition">
            <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>

<!-- Modal Panduan Instalasi PWA untuk iPhone / iPad -->
<div id="modal-pwa-ios"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-pwa-ios-title"
     onclick="if (event.target === this) closeIosInstallModal()"
     class="hidden fixed inset-0 z-[60] overflow-y-auto bg-slate-900/60 backdrop-blur-sm items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white rounded-t-3xl sm:rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-2xl border border-slate-200 space-y-4">
        <div class="w-10 h-1 rounded-full bg-slate-200 mx-auto sm:hidden"></div>

        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-3">
                <img src="{{ asset('icon-apple-touch.png') }}"
                     alt="Ikon Aplikasi Dusun Demangan"
                     width="40" height="40"
                     class="w-10 h-10 rounded-xl border border-emas-300 shadow-sm shrink-0 bg-white">
                <div>
                    <h3 id="modal-pwa-ios-title" class="text-base font-bold text-slate-900 leading-tight">
                        Pasang Aplikasi di iPhone
                    </h3>
                    <p class="text-xs text-slate-600 mt-0.5">Tambahkan ke Layar Utama Safari dalam 3 langkah</p>
                </div>
            </div>
            <button type="button"
                    onclick="closeIosInstallModal()"
                    aria-label="Tutup panduan pasang iPhone"
                    class="p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl min-h-[44px] min-w-[44px] flex items-center justify-center transition">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Peringatan Jika Dibuka Dari Browser Dalam Aplikasi (WhatsApp / IG / dll) -->
        <div id="ios-inapp-warning" class="hidden rounded-xl bg-emas-50 border border-emas-300 p-3 text-xs text-emas-950 space-y-2">
            <p class="font-bold text-emas-900">Terdeteksi dibuka di browser dalam aplikasi</p>
            <p class="text-[11px] leading-relaxed text-emas-900">
                Untuk memasang di iPhone, buka halaman ini terlebih dahulu di browser <strong>Safari</strong>.
            </p>
            <button type="button"
                    id="btn-copy-pwa-url"
                    onclick="copyCurrentUrlForSafari()"
                    class="btn-secondary w-full text-xs py-2 min-h-[40px]">
                <svg class="w-4 h-4 text-emas-800" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span id="copy-pwa-url-label">Salin Link untuk Dibuka di Safari</span>
            </button>
        </div>

        <!-- Langkah Visual Safari iOS -->
        <ol class="space-y-2.5 text-xs text-slate-700">
            <li class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200/80">
                <span class="w-6 h-6 rounded-full bg-emas-500 text-[#FAF4E4CC] font-extrabold flex items-center justify-center shrink-0 tabular-money">1</span>
                <div class="flex-1 leading-relaxed">
                    <div class="font-bold text-slate-900 flex items-center gap-1.5 flex-wrap">
                        <span>Ketuk ikon Bagikan (Share)</span>
                        <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded bg-sky-100 text-sky-800 border border-sky-200">
                            <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 4v12m0-12l-4 4m4-4l4 4"/>
                            </svg>
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-600 mt-0.5">Berada di bar menu bagian bawah layar Safari.</p>
                </div>
            </li>

            <li class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200/80">
                <span class="w-6 h-6 rounded-full bg-emas-500 text-[#FAF4E4CC] font-extrabold flex items-center justify-center shrink-0 tabular-money">2</span>
                <div class="flex-1 leading-relaxed">
                    <div class="font-bold text-slate-900 flex items-center gap-1.5 flex-wrap">
                        <span>Pilih "Tambah ke Layar Utama"</span>
                        <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded bg-slate-200 text-slate-800">
                            <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-4 5h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-600 mt-0.5">Geser daftar menu Share ke bawah (atau bahasa Inggris: <em>Add to Home Screen</em>).</p>
                </div>
            </li>

            <li class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200/80">
                <span class="w-6 h-6 rounded-full bg-emas-500 text-[#FAF4E4CC] font-extrabold flex items-center justify-center shrink-0 tabular-money">3</span>
                <div class="flex-1 leading-relaxed">
                    <div class="font-bold text-slate-900">Ketuk "Tambah" (Add) di pojok kanan atas</div>
                    <p class="text-[11px] text-slate-600 mt-0.5">Ikon <strong>Dusun Demangan</strong> akan langsung muncul di layar utama iPhone Anda.</p>
                </div>
            </li>
        </ol>

        <button type="button" onclick="closeIosInstallModal()" class="btn-primary w-full text-xs min-h-[46px]">
            Mengerti, Tutup Panduan
        </button>
    </div>
</div>

<script>
    (function () {
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }

        const triggerId = @json($triggerButtonId);
        const triggerDisplayClass = @json($triggerDisplayClass);
        const ua = navigator.userAgent || '';
        const isIos = /iphone|ipad|ipod/i.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
        const isStandalone = window.navigator.standalone === true || window.matchMedia('(display-mode: standalone)').matches;
        const isInAppBrowser = /(FBAN|FBAV|Instagram|Line|WhatsApp|MicroMessenger|Twitter|TikTok)/i.test(ua);
        const DISMISS_KEY = 'demangan_pwa_ios_dismissed_at';
        const SEVEN_DAYS_MS = 7 * 24 * 60 * 60 * 1000;

        let deferredPwaPrompt = null;

        function showTriggerButton() {
            const btn = document.getElementById(triggerId);
            if (btn) {
                btn.classList.remove('hidden');
                btn.classList.add(triggerDisplayClass);
            }
        }

        function hideTriggerButton() {
            const btn = document.getElementById(triggerId);
            if (btn) {
                btn.classList.add('hidden');
                btn.classList.remove(triggerDisplayClass);
            }
        }

        window.openIosInstallModal = function () {
            if (typeof window.closeMobileMenu === 'function') {
                window.closeMobileMenu();
            }
            const modal = document.getElementById('modal-pwa-ios');
            const warning = document.getElementById('ios-inapp-warning');
            if (warning) {
                if (isInAppBrowser) {
                    warning.classList.remove('hidden');
                } else {
                    warning.classList.add('hidden');
                }
            }
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        };

        window.closeIosInstallModal = function () {
            const modal = document.getElementById('modal-pwa-ios');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        };

        window.dismissIosPwaBanner = function () {
            const banner = document.getElementById('ios-pwa-banner');
            if (banner) {
                banner.classList.add('hidden');
                banner.classList.remove('flex');
            }
            try {
                localStorage.setItem(DISMISS_KEY, String(Date.now()));
            } catch (e) {}
        };

        window.copyCurrentUrlForSafari = async function () {
            const label = document.getElementById('copy-pwa-url-label');
            try {
                await navigator.clipboard.writeText(window.location.href);
                if (label) {
                    label.textContent = 'Link Tersalin! Buka di Safari';
                    setTimeout(() => {
                        label.textContent = 'Salin Link untuk Dibuka di Safari';
                    }, 3000);
                }
            } catch (e) {
                if (label) label.textContent = window.location.href;
            }
        };

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                window.closeIosInstallModal();
            }
        });

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPwaPrompt = e;
            showTriggerButton();
        });

        async function handlePwaInstallClick() {
            if (deferredPwaPrompt) {
                deferredPwaPrompt.prompt();
                const choice = await deferredPwaPrompt.userChoice;
                if (choice.outcome === 'accepted') {
                    hideTriggerButton();
                }
                deferredPwaPrompt = null;
            } else {
                window.openIosInstallModal();
            }
        }

        window.triggerPwaInstallPublik = handlePwaInstallClick;
        window.triggerPwaInstall = handlePwaInstallClick;

        window.addEventListener('appinstalled', () => {
            hideTriggerButton();
            deferredPwaPrompt = null;
        });

        // Pada iPhone/iPad yang belum terpasang sebagai aplikasi (non-standalone),
        // tampilkan tombol Pasang App di header/drawer dan banner petunjuk ringkas.
        if (isIos && !isStandalone) {
            showTriggerButton();

            let recentlyDismissed = false;
            try {
                const dismissedAt = parseInt(localStorage.getItem(DISMISS_KEY) || '0', 10);
                if (dismissedAt && (Date.now() - dismissedAt) < SEVEN_DAYS_MS) {
                    recentlyDismissed = true;
                }
            } catch (e) {}

            if (!recentlyDismissed) {
                const banner = document.getElementById('ios-pwa-banner');
                if (banner) {
                    banner.classList.remove('hidden');
                    banner.classList.add('flex');
                }
            }
        }
    })();
</script>
