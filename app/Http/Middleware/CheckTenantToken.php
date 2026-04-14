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
        $token = trim($request->bearerToken() ?? '');

        if (!$token) {
            logger()->warning('CheckTenantToken: No se proporcionó token.');
            return response()->json(['message' => 'Token de acceso no proporcionado.'], 401);
        }

        $tenant = \App\Models\Tenant::where('api_token', $token)
            ->orWhere('id', $token)
            ->first();

        if (!$tenant) {
            $maskedToken = substr($token, 0, 5) . '...' . substr($token, -5);
            logger()->warning("CheckTenantToken: Token inválido [Len:" . strlen($token) . "]: " . $maskedToken);
            return response()->json([
                'message' => 'Token de acceso inválido.',
                'debug' => [
                    'received_length' => strlen($token),
                    'sent_token_mask' => $maskedToken
                ]
            ], 401);
        }

        if (!$tenant->status) {
            return response()->json(['message' => 'La empresa está inactiva.'], 403);
        }

        // Compartir el tenant en el request para uso posterior
        $request->merge(['current_tenant' => $tenant]);

        return $next($request);
    }
}
