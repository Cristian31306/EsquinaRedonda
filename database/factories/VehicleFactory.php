<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plate' => strtoupper($this->faker->unique()->bothify('???###')),
            'type' => $this->faker->randomElement(['carro', 'moto', 'pesado']),
            'observation' => $this->faker->optional(0.3)->sentence(),
        ];
    }
}
