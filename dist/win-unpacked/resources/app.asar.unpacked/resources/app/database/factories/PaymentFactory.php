<?php

namespace Database\Factories;

use App\Models\CashShift;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'cash_shift_id' => CashShift::factory(),
            'amount' => $this->faker->numberBetween(2000, 50000),
            'payment_method' => 'cash',
        ];
    }
}
