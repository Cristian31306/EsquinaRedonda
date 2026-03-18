<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Payment;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['vehicle_id', 'entry_time', 'exit_time', 'status', 'user_id'];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
