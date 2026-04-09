<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'status',
        'plan',
        'billing_cycle',
        'expires_at',
        'nit',
        'address',
        'phone',
        'social_handle',
        'tax_regime',
        'business_hours',
        'welcome_message',
        'disclaimer_message',
    ];

    /**
     * Verifica si el inquilino puede exportar reportes (Solo Plan Pro).
     */
    public function canExportReports(): bool
    {
        return $this->plan === 'pro';
    }

    /**
     * Verifica si el inquilino puede acceder a la auditoría detallada (Solo Plan Pro).
     */
    public function canAccessAuditing(): bool
    {
        return $this->plan === 'pro';
    }

    /**
     * Obtiene el límite de usuarios basado en el plan.
     */
    public function getMaxUsersAttribute(): int
    {
        return $this->plan === 'basico' ? 3 : 999;
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
