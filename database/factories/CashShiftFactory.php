<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CashShift>
 */
class CashShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 year', 'now');
        $end = $this->faker->optional(0.9)->dateTimeBetween($start, '+8 hours');
        
        return [
            'user_id' => User::factory(),
            'start_time' => $start,
            'end_time' => $end,
            'opening_cash' => $this->faker->numberBetween(50000, 200000),
            'closing_cash_declared' => $end ? $this->faker->numberBetween(200000, 1000000) : null,
            'status' => $end ? 'closed' : 'open',
        ];
    }
}
