<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'status',
        'plan',
        'billing_cycle',
        'expires_at',
        'nit',
        'address',
        'phone',
        'social_handle',
        'tax_regime',
        'business_hours',
        'welcome_message',
        'disclaimer_message',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
