<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RateController extends Controller
{
    public function index()
    {
        return Inertia::render('Rates/Index', [
            'rates' => Rate::all()->groupBy('vehicle_type')
        ]);
    }

    public function storeBulk(Request $request)
    {
        $request->validate([
            'vehicle_type' => 'required|string',
            'rates' => 'required|array',
        ]);

        $vehicleType = strtolower($request->vehicle_type);

        foreach ($request->rates as $concept => $value) {
            Rate::updateOrCreate(
                ['vehicle_type' => $vehicleType, 'concept' => $concept],
                ['value' => $value, 'is_active' => $value > 0]
            );
        }

        return back()->with('success', 'Tarifas actualizadas correctamente.');
    }

    public function destroyCategory($vehicleType)
    {
        Rate::where('vehicle_type', $vehicleType)->delete();
        return back()->with('success', "Categoría {$vehicleType} eliminada.");
    }

    public function update(Request $request, Rate $rate)
// ...
    {
        $request->validate([
            'value' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        $rate->update($request->only('value', 'is_active'));

        return back()->with('success', 'Tarifa actualizada correctamente.');
    }

    public function destroy(Rate $rate)
    {
        $rate->delete();
        return back()->with('success', 'Tarifa eliminada.');
    }
}
