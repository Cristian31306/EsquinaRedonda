<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Ticket;
use App\Models\Vehicle;
use App\Models\Rate;
use App\Models\User;
use App\Services\BillingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BillingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $billingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->billingService = new BillingService();
    }

    public function test_calculate_total_for_15_min_fraction()
    {
        $vehicle = Vehicle::create(['plate' => 'TEST1', 'type' => 'carro']);
        $user = User::factory()->create();
        
        // Tarifa: Fracción $2000, Hora $6000, Día $30000
        Rate::create(['vehicle_type' => 'carro', 'concept' => 'fraccion', 'value' => 2000]);
        Rate::create(['vehicle_type' => 'carro', 'concept' => 'hora', 'value' => 6000]);
        Rate::create(['vehicle_type' => 'carro', 'concept' => 'dia', 'value' => 30000]);

        $entry = Carbon::now()->subMinutes(10);
        $ticket = Ticket::create(['vehicle_id' => $vehicle->id, 'entry_time' => $entry, 'user_id' => $user->id]);

        $total = $this->billingService->calculateCurrentTotal($ticket);
        $this->assertEquals(2000, $total); // 10 min = 1 fraccion
    }

    public function test_calculate_total_for_multiple_fractions()
    {
        $vehicle = Vehicle::create(['plate' => 'TEST2', 'type' => 'carro']);
        $user = User::factory()->create();
        
        Rate::create(['vehicle_type' => 'carro', 'concept' => 'fraccion', 'value' => 2000]);
        Rate::create(['vehicle_type' => 'carro', 'concept' => 'hora', 'value' => 6000]);

        $entry = Carbon::now()->subMinutes(40);
        $ticket = Ticket::create(['vehicle_id' => $vehicle->id, 'entry_time' => $entry, 'user_id' => $user->id]);

        $total = $this->billingService->calculateCurrentTotal($ticket);
        $this->assertEquals(3 * 2000, $total); // 40 min = 3 fracciones (2 full + 1 partial)
    }

    public function test_calculate_total_caps_at_daily_rate()
    {
        $vehicle = Vehicle::create(['plate' => 'TEST3', 'type' => 'carro']);
        $user = User::factory()->create();
        
        Rate::create(['vehicle_type' => 'carro', 'concept' => 'fraccion', 'value' => 2000]);
        Rate::create(['vehicle_type' => 'carro', 'concept' => 'hora', 'value' => 6000]);
        Rate::create(['vehicle_type' => 'carro', 'concept' => 'dia', 'value' => 20000]);

        // 10 horas costarian $60k, pero el tope es $20k
        $entry = Carbon::now()->subHours(10);
        $ticket = Ticket::create(['vehicle_id' => $vehicle->id, 'entry_time' => $entry, 'user_id' => $user->id]);

        $total = $this->billingService->calculateCurrentTotal($ticket);
        $this->assertEquals(20000, $total);
    }
}
