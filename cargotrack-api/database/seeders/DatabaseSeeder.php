<?php

namespace Database\Seeders;

use App\Models\Courier;
use App\Models\Shipment;
use App\Models\ShipmentLog;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat 3 warehouse dummy
        Warehouse::factory()->count(3)->create();

        // 2. Buat 3 akun utama (1 per role)
        $admin = User::factory()->admin()->create([
            'name' => 'Admin CargoTrack',
            'email' => 'admin@cargotrack.test',
        ]);

        $warehouseForStaff = Warehouse::first();

        User::factory()->warehouseStaff()->create([
            'name' => 'Staff Gudang',
            'email' => 'staff@cargotrack.test',
            'warehouse_id' => $warehouseForStaff?->id,
        ]);

        $courierUser = User::factory()->courier()->create([
            'name' => 'Kurir Utama',
            'email' => 'courier@cargotrack.test',
        ]);

        Courier::factory()->create([
            'user_id' => $courierUser->id,
        ]);

        // 3. Buat 3 courier tambahan
        Courier::factory()->count(3)->create();

        // 4. Ambil ulang data langsung dari database
        $warehouses = Warehouse::all();
        $couriers = Courier::all();

        if ($couriers->isEmpty() || $warehouses->isEmpty()) {
            $this->command->error('Warehouse atau Courier kosong, seeding shipment dibatalkan.');
            return;
        }

        // 5. Buat 20 shipment dummy
        Shipment::factory()
            ->count(20)
            ->create()
            ->each(function (Shipment $shipment) use ($warehouses, $couriers, $admin) {
                if (fake()->boolean(60)) {
                    $shipment->update([
                        'current_warehouse_id' => $warehouses->random()->id,
                        'assigned_courier_id' => $couriers->random()->id,
                    ]);
                }

                ShipmentLog::create([
                    'shipment_id' => $shipment->id,
                    'status' => $shipment->current_status,
                    'location_description' => fake()->city(),
                    'notes' => 'Status awal saat shipment dibuat.',
                    'created_by_user_id' => $admin->id,
                ]);
            });
    }
}