<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rate extends Model
{
    use HasFactory;

    protected $fillable = ['vehicle_type', 'concept', 'value', 'is_active'];
}
