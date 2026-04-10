<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Payment;

use App\Traits\BelongsToTenant;
use App\Traits\HasUuid;

class CashShift extends Model
{
    use HasFactory, BelongsToTenant, HasUuid;

    protected $fillable = ['user_id', 'start_time', 'end_time', 'opening_cash', 'closing_cash_declared', 'status'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalCollectedAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getTotalCashAttribute()
    {
        return $this->payments()->where('payment_method', 'efectivo')->sum('amount');
    }

    public function getTotalTransferAttribute()
    {
        return $this->payments()->where('payment_method', 'trasnferencia')->sum('amount');
    }
}
