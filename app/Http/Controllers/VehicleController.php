<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Membership;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function check(string $plate)
    {
        $plate = strtoupper(trim($plate));
        
        $vehicle = Vehicle::where('plate', $plate)->first();
        
        $membership = null;
        if ($vehicle) {
            $membership = Membership::where('vehicle_id', $vehicle->id)
                ->active()
                ->first();
        }

        return response()->json([
            'exists' => !!$vehicle,
            'type' => $vehicle?->type,
            'has_membership' => !!$membership,
            'membership_info' => $membership ? [
                'end_date' => $membership->end_date->toDateString(),
                'days_left' => (int) now()->diffInDays($membership->end_date, false),
            ] : null,
        ]);
    }
}
