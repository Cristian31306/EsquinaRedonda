<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->tenant_id) {
            $tenant = auth()->user()->tenant;
            
            if ($tenant && $tenant->status === 'suspended') {
                auth()->logout();
                return redirect()->route('login')->with('error', 'Su cuenta de empresa ha sido suspendida. Contacte a Algorah para más información.');
            }
        }

        return $next($request);
    }
}
