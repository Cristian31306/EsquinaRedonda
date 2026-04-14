<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\CashShift;
use Symfony\Component\HttpFoundation\Response;

class EnsureCashShiftIsOpen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $activeShift = CashShift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if (!$activeShift) {
            $message = 'Debe abrir caja antes de realizar esta operación de cobro.';

            if ($request->expectsJson()) {
                return response()->json(['error' => $message], 403);
            }

            return back()->with('error', $message);
        }

        return $next($request);
    }
}
