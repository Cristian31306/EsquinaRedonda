<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\BelongsToTenant;

class Rate extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = ['vehicle_type', 'concept', 'value', 'is_active', 'tenant_id'];
}
