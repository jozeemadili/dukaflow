<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsMerchant
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isMerchantUser()) {
            abort(403, 'This area is restricted to merchant accounts.');
        }

        return $next($request);
    }
}
