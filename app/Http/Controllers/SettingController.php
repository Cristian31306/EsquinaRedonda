<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::getAllCached();
        
        return Inertia::render('Settings/Index', [
            'settings' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'business_name' => 'nullable|string|max:255',
            'business_nit' => 'nullable|string|max:50',
            'business_address' => 'nullable|string|max:255',
            'business_phone' => 'nullable|string|max:50',
            'is_iva_responsible' => 'nullable|string|max:10',
            'business_schedule' => 'nullable|string|max:255',
            'ticket_footer' => 'nullable|string|max:500',
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Configuración actualizada correctamente.');
    }
}
