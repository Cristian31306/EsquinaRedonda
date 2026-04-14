<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? $request->user()->load('tenant') : null,
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
                'print_ticket' => session('print_ticket'),
                'printShift' => session('printShift'),
            ],
            'settings' => auth()->check() ? \App\Models\Setting::getAllCached() : collect(),
            'sync_token' => \Illuminate\Support\Facades\DB::table('settings')->where('key', 'tenant_sync_token')->value('value'),
            'is_native' => env('NATIVEPHP_RUNNING', false) || env('NATIVEPHP_DESKTOP_BUILD', false),
        ];
    }
}
