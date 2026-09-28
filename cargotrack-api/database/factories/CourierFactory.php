<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class CourierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'user_id' => User::factory()->courier(),
            'vehicle_type' => $this->faker->randomElement(['motorcycle', 'van', 'truck']),
            'license_plate' => strtoupper($this->faker->bothify('B-####-??')),
            'status' => 'available',
        ];
    }
}
