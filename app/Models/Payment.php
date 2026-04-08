<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ticket;
use App\Models\CashShift;

use App\Traits\BelongsToTenant;

class Payment extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = ['ticket_id', 'cash_shift_id', 'amount', 'payment_method', 'tenant_id'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function cashShift()
    {
        return $this->belongsTo(CashShift::class);
    }
}
