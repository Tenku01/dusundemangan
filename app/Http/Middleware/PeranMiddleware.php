<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PeranMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$peran): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->status !== 'aktif') {
            auth()->logout();

            return redirect()->route('login')->withErrors(['email' => 'Akun Anda dinonaktifkan oleh pengurus.']);
        }

        if (! in_array($request->user()->peran, $peran, true)) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
