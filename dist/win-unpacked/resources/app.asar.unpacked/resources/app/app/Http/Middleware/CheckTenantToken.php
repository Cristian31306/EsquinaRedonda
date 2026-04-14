<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token de acceso no proporcionado.'], 401);
        }

        $tenant = \App\Models\Tenant::where('api_token', $token)->first();

        if (!$tenant) {
            return response()->json(['message' => 'Token de acceso inválido.'], 401);
        }

        if (!$tenant->status) {
            return response()->json(['message' => 'La empresa está inactiva.'], 403);
        }

        // Compartir el tenant en el request para uso posterior
        $request->merge(['current_tenant' => $tenant]);

        return $next($request);
    }
}
