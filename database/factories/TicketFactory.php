<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $entry = $this->faker->dateTimeBetween('-1 year', 'now');
        $exit = $this->faker->optional(0.8)->dateTimeBetween($entry, 'now');
        
        return [
            'vehicle_id' => Vehicle::factory(),
            'user_id' => User::factory(),
            'entry_time' => $entry,
            'exit_time' => $exit,
            'status' => $exit ? 'completed' : 'active',
            'stay_type' => $this->faker->randomElement([null, 'overnight', 'fullday']),
        ];
    }
}
