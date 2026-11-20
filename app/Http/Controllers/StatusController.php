<?php

namespace App\Http\Controllers;

use App\Http\Middleware\MonitorMethodStatus;
use App\Models\Barang;
use App\Models\DataChangeLog;
use App\Models\Keuangan;
use App\Models\MethodStatusLog;
use App\Models\Peminjaman;
use App\Models\TagihanListrik;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StatusController extends Controller
{
    /**
     * Menampilkan pusat pemantauan sistem (/status):
     * 1. Log Perubahan Data (prioritas siapa yang mengubah data)
     * 2. Status Method & Memori
     * 3. Status Update Web (permanen / tidak dapat dihapus)
     */
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'perubahan');
        if (! in_array($tab, ['perubahan', 'method', 'update'], true)) {
            $tab = 'perubahan';
        }

        $aksiFilter = $request->query('aksi');
        $modelFilter = $request->query('model');
        $cariFilter = trim((string) $request->query('cari', ''));

        $dataTableExists = Schema::hasTable('data_change_logs');
        $methodTableExists = Schema::hasTable('method_status_logs');

        // 1. Data Perubahan Log (Prioritas)
        $dataLogs = null;
        $totalDataLogs = 0;

        if ($dataTableExists) {
            $totalDataLogs = DataChangeLog::count();

            $queryData = DataChangeLog::query();

            if ($aksiFilter && in_array($aksiFilter, ['created', 'updated', 'deleted'], true)) {
                $queryData->where('aksi', $aksiFilter);
            }

            if ($modelFilter) {
                $queryData->where('model_label', $modelFilter);
            }

            if ($cariFilter !== '') {
                $queryData->where(function ($q) use ($cariFilter) {
                    $q->where('user_name', 'like', "%{$cariFilter}%")
                        ->orWhere('user_email', 'like', "%{$cariFilter}%")
                        ->orWhere('subject_label', 'like', "%{$cariFilter}%")
                        ->orWhere('model_label', 'like', "%{$cariFilter}%");
                });
            }

            $dataLogs = $queryData->orderByDesc('created_at')
                ->orderByDesc('id')
                ->paginate(15, ['*'], 'page_data')
                ->withQueryString();
        } else {
            $dataLogs = DataChangeLog::whereRaw('1 = 0')->paginate(15);
        }

        // 2. Status Method & Memori
        $methodLogs = null;
        $totalMethodLogs = 0;
        $methodSummaryByVerb = collect();
        $methodStatsByRoute = collect();

        if ($methodTableExists) {
            $totalMethodLogs = MethodStatusLog::count();

            $methodSummaryByVerb = MethodStatusLog::selectRaw('
                http_method,
                COUNT(*) as total_hit,
                ROUND(AVG(duration_ms), 0) as avg_ms,
                SUM(CASE WHEN status_code >= 400 THEN 1 ELSE 0 END) as total_error
            ')
                ->groupBy('http_method')
                ->orderByDesc('total_hit')
                ->get();

            $methodStatsByRoute = MethodStatusLog::selectRaw('
                route_name,
                controller_action,
                http_method,
                COUNT(*) as total_hit,
                ROUND(AVG(duration_ms), 0) as avg_ms,
                MAX(status_code) as last_status,
                MAX(created_at) as last_hit
            ')
                ->groupBy('route_name', 'controller_action', 'http_method')
                ->orderByDesc('total_hit')
                ->take(30)
                ->get();

            $methodLogs = MethodStatusLog::orderByDesc('created_at')
                ->orderByDesc('id')
                ->paginate(20, ['*'], 'page_method')
                ->withQueryString();
        } else {
            $methodLogs = MethodStatusLog::whereRaw('1 = 0')->paginate(20);
        }

        $registeredMethods = collect(Route::getRoutes()->getRoutes())
            ->filter(function ($route) {
                $uri = $route->uri();

                return ! str_starts_with($uri, '_') && $uri !== 'up';
            })
            ->map(function ($route) {
                $action = $route->getActionName();
                $shortAction = str_contains($action, '@')
                    ? class_basename(explode('@', $action)[0]).'@'.explode('@', $action)[1]
                    : (str_contains($action, 'Closure') ? 'Closure' : class_basename($action));

                return [
                    'methods' => array_values(array_diff($route->methods(), ['HEAD'])),
                    'uri' => '/'.ltrim($route->uri(), '/'),
                    'name' => $route->getName() ?? '-',
                    'action' => $shortAction,
                ];
            })
            ->values();

        // Informasi Memori Runtime PHP
        $memoryUsageBytes = memory_get_usage(true);
        $memoryPeakBytes = memory_get_peak_usage(true);
        $memoryLimit = ini_get('memory_limit') ?: '128M';

        // 3. Status Update Web (Immutable dari config)
        $currentVersion = config('web_updates.current_version', 'v2.4.0');
        $webUpdates = config('web_updates.releases', []);

        $daftarModelLabel = [
            'Tagihan Listrik',
            'Data Warga',
            'Buku Kas',
            'Inventaris Barang',
            'Peminjaman Barang',
            'Akun Pengurus',
        ];

        $modelCounts = [
            'Tagihan Listrik' => TagihanListrik::count(),
            'Data Warga' => Warga::count(),
            'Buku Kas' => Keuangan::count(),
            'Inventaris Barang' => Barang::count(),
            'Peminjaman Barang' => Peminjaman::count(),
            'Akun Pengurus' => User::count(),
        ];

        return view('status.index', compact(
            'tab',
            'aksiFilter',
            'modelFilter',
            'cariFilter',
            'dataTableExists',
            'methodTableExists',
            'dataLogs',
            'totalDataLogs',
            'methodLogs',
            'totalMethodLogs',
            'methodSummaryByVerb',
            'methodStatsByRoute',
            'registeredMethods',
            'memoryUsageBytes',
            'memoryPeakBytes',
            'memoryLimit',
            'currentVersion',
            'webUpdates',
            'daftarModelLabel',
            'modelCounts'
        ));
    }

    /**
     * Membersihkan seluruh log perubahan data.
     */
    public function clearDataLogs(): RedirectResponse
    {
        $deletedCount = 0;

        if (Schema::hasTable('data_change_logs')) {
            $deletedCount = DataChangeLog::count();
            DataChangeLog::query()->delete();
        }

        return redirect()->route('status.index', ['tab' => 'perubahan'])
            ->with('sukses', "Berhasil membersihkan {$deletedCount} catatan log perubahan data.");
    }

    /**
     * Membersihkan seluruh log eksekusi method untuk menghemat penyimpanan & memori.
     */
    public function clearMethodLogs(): RedirectResponse
    {
        $deletedCount = 0;

        if (Schema::hasTable('method_status_logs')) {
            $deletedCount = MethodStatusLog::count();
            MethodStatusLog::query()->delete();
        }

        MonitorMethodStatus::resetTableAvailabilityCache();

        return redirect()->route('status.index', ['tab' => 'method'])
            ->with('sukses', "Berhasil membersihkan {$deletedCount} catatan status method.");
    }

    /**
     * Membersihkan log method, cache view/aplikasi, serta menjalankan GC PHP untuk memperluas memori.
     */
    public function clearMemory(): RedirectResponse
    {
        $deletedMethodLogs = 0;

        if (Schema::hasTable('method_status_logs')) {
            $deletedMethodLogs = MethodStatusLog::count();
            MethodStatusLog::query()->delete();
        }

        Cache::flush();
        Artisan::call('view:clear');

        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }

        MonitorMethodStatus::resetTableAvailabilityCache();

        $memoryNowMb = number_format(memory_get_usage(true) / 1048576, 2);

        return redirect()->route('status.index', ['tab' => 'method'])
            ->with('sukses', "Memori dan cache berhasil dibersihkan ({$deletedMethodLogs} log method dihapus, memori aktif saat ini {$memoryNowMb} MB).");
    }
}
