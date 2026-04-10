<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToTenant;
use App\Traits\HasUuid;

class Vehicle extends Model
{
    use HasFactory, BelongsToTenant, HasUuid;

    protected $fillable = ['plate', 'type', 'observation', 'tenant_id'];

    /**
     * Set the vehicle's plate to uppercase.
     */
    protected function plate(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            set: fn (string $value) => strtoupper($value),
        );
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
