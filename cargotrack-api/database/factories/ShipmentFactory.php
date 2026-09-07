<?php

namespace Database\Factories;

//use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
 */
class ShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * return array<string, mixed>
     */
    public function definition(): array
    {
                $weight = $this->faker->randomFloat(2, 0.5, 50);
        $baseFee = 10000;
        $ratePerKg = 5000;
        $cost = $baseFee + ($weight * $ratePerKg);

        return [
            'tracking_number' => 'CT-' . now()->format('Ymd') . '-' . strtoupper($this->faker->unique()->bothify('####')),
            'sender_name' => $this->faker->name(),
            'sender_phone' => $this->faker->numerify('08##########'),
            'sender_address' => $this->faker->address(),
            'origin_city' => $this->faker->city(),
            'receiver_name' => $this->faker->name(),
            'receiver_phone' => $this->faker->numerify('08##########'),
            'receiver_address' => $this->faker->address(),
            'destination_city' => $this->faker->city(),
            'weight_kg' => $weight,
            'item_type' => $this->faker->randomElement(['Dokumen', 'Elektronik', 'Pakaian', 'Makanan', 'Lainnya']),
            'current_status' => $this->faker->randomElement([
                'pending', 'received_at_warehouse', 'in_transit', 'out_for_delivery', 'delivered', 'failed',
            ]),
            'shipping_cost' => $cost,
            'current_warehouse_id' => null,
            'assigned_courier_id' => null,
        ];
    }
}
