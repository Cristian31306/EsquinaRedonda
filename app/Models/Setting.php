<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\BelongsToTenant;

class Setting extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = ['key', 'value', 'tenant_id'];

    /**
     * Obtiene todos los ajustes del inquilino actual usando caché.
     */
    public static function getAllCached()
    {
        $tenantId = auth()->user()?->tenant_id;
        if (!$tenantId) return collect();

        return \Illuminate\Support\Facades\Cache::remember("settings_tenant_{$tenantId}", 3600, function () {
            return self::all()->pluck('value', 'key');
        });
    }

    /**
     * Limpia la caché al guardar cambios.
     */
    protected static function booted()
    {
        static::saved(function ($setting) {
            \Illuminate\Support\Facades\Cache::forget("settings_tenant_{$setting->tenant_id}");
        });

        static::deleted(function ($setting) {
            \Illuminate\Support\Facades\Cache::forget("settings_tenant_{$setting->tenant_id}");
        });
    }
}
