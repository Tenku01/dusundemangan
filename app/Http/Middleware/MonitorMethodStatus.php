<?php

namespace App\Http\Middleware;

use App\Models\MethodStatusLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MonitorMethodStatus
{
    private static ?bool $tableAvailable = null;

    public static function resetTableAvailabilityCache(): void
    {
        self::$tableAvailable = null;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        $response = $next($request);

        $this->recordMethodStatus($request, $response, $startTime, $startMemory);

        return $response;
    }

    private function recordMethodStatus(Request $request, Response $response, float $startTime, int $startMemory): void
    {
        $path = ltrim($request->path(), '/');

        if ($this->shouldSkipPath($path)) {
            return;
        }

        try {
            if (self::$tableAvailable === null) {
                self::$tableAvailable = Schema::hasTable('method_status_logs');
            }

            if (! self::$tableAvailable) {
                return;
            }

            $route = $request->route();
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            $memoryKb = (int) max(0, round((memory_get_usage(true) - $startMemory) / 1024));
            $user = $request->user();

            MethodStatusLog::create([
                'http_method' => strtoupper($request->method()),
                'route_name' => $route?->getName(),
                'controller_action' => $route ? mb_substr((string) $route->getActionName(), 0, 255) : 'Closure/Unmatched',
                'path' => '/'.$path,
                'status_code' => $response->getStatusCode(),
                'duration_ms' => max(0, $durationMs),
                'memory_kb' => $memoryKb,
                'user_id' => $user?->id,
                'user_name' => $user?->name ?? 'Publik / Tamu',
                'ip_address' => $request->ip(),
                'created_at' => now(),
            ]);
        } catch (Throwable) {
            // Jangan mengganggu respons HTTP jika pencatatan telemetry tidak tersedia
        }
    }

    private function shouldSkipPath(string $path): bool
    {
        foreach (['up', 'build/', 'sw.js', 'manifest.', 'favicon', '_ignition'] as $ignored) {
            if ($path === $ignored || str_starts_with($path, $ignored)) {
                return true;
            }
        }

        return false;
    }
}
