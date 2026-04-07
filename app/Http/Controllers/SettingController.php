<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        
        return Inertia::render('Settings/Index', [
            'settings' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'telegram_bot_token' => 'nullable|string',
            'telegram_chat_ids' => 'nullable|string',
            'backup_time' => 'nullable|string',
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Configuración actualizada correctamente.');
    }
    public function backupNow()
    {
        \App\Jobs\BackupToTelegram::dispatch();
        return back()->with('success', 'Copia de seguridad encolada correctamente. Recibirás el archivo en Telegram en un momento.');
    }
}
