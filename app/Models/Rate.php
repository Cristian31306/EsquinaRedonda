<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\BelongsToTenant;
use App\Traits\HasUuid;

class Rate extends Model
{
    use HasFactory, BelongsToTenant, HasUuid;

    protected $fillable = ['vehicle_type', 'concept', 'value', 'is_active', 'tenant_id'];
}
