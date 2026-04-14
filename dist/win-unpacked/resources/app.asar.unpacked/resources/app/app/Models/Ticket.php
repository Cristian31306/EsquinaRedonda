<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Payment;

use App\Traits\BelongsToTenant;
use App\Traits\HasUuid;

class Ticket extends Model
{
    use HasFactory, BelongsToTenant, HasUuid;

    protected $fillable = ['vehicle_id', 'entry_time', 'exit_time', 'status', 'user_id', 'stay_type', 'tenant_id'];

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
