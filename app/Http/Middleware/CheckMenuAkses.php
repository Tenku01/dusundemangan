<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuAkses
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $menu): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->hasMenuAkses($menu)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Anda tidak memiliki hak akses untuk membuka menu ini.',
                ], 403);
            }

            $tujuan = $user->getFirstAccessibleRoute();

            return redirect()->route($tujuan)
                ->withErrors(['akses' => 'Anda tidak memiliki hak akses untuk membuka menu tersebut. Silakan hubungi Superadmin.']);
        }

        return $next($request);
    }
}
