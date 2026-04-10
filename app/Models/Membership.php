<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use App\Traits\BelongsToTenant;
use App\Traits\HasUuid;

class Membership extends Model
{
    use HasFactory, BelongsToTenant, HasUuid;

    protected $fillable = [
        'vehicle_id', 'plate', 'vehicle_type',
        'start_date', 'end_date', 'amount_paid',
        'cash_shift_id', 'notes',
        'tenant_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function cashShift()
    {
        return $this->belongsTo(CashShift::class);
    }

    /**
     * Scope para membresías activas (end_date >= hoy)
     */
    public function scopeActive($query)
    {
        return $query->where('end_date', '>=', Carbon::today());
    }

    /**
     * Verificar si esta membresía está actualmente activa
     */
    public function isActive(): bool
    {
        return $this->end_date->gte(Carbon::today());
    }
}
