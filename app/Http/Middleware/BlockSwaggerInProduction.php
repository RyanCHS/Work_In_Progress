<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockSwaggerInProduction
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->environment('production')) {
            abort(404);  // Menolak akses dengan halaman 404
        }
        return $next($request);
    }
}
