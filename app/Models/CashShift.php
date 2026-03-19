<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Payment;

class CashShift extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'start_time', 'end_time', 'opening_cash', 'closing_cash_declared', 'status'];

    protected $appends = ['total_collected', 'total_cash', 'total_transfer'];

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
