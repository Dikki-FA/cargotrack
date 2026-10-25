<?php

use App\Models\Courier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
it('courier ditolak mengubah data master warehouse (403)', function () {
    $courierUser = User::factory()->courier()->create();
    Courier::factory()->create(['user_id' => $courierUser->id]);

    $this->actingAs($courierUser, 'sanctum')
        ->postJson('/api/v1/warehouses', [
            'code' => 'WH-999',
            'name' => 'Gudang Test',
            'address' => 'Jl. Test',
            'city' => 'Jakarta',
            'phone' => '08123456789',
        ])
        ->assertForbidden();
});
*/

it('courier hanya bisa update status shipment yang ditugaskan kepadanya', function () {
    $courierUser = User::factory()->courier()->create();
    $courier = Courier::factory()->create(['user_id' => $courierUser->id]);
    $otherCourier = Courier::factory()->create();

    $ownShipment = \App\Models\Shipment::factory()->create([
        'assigned_courier_id' => $courier->id,
    ]);
    $otherShipment = \App\Models\Shipment::factory()->create([
        'assigned_courier_id' => $otherCourier->id,
    ]);

    $this->actingAs($courierUser, 'sanctum')
        ->patchJson("/api/v1/shipments/{$ownShipment->id}/status", ['status' => 'delivered'])
        ->assertOk();

    $this->actingAs($courierUser, 'sanctum')
        ->patchJson("/api/v1/shipments/{$otherShipment->id}/status", ['status' => 'delivered'])
        ->assertForbidden();
});

it('warehouse staff tidak bisa update status ke delivered', function () {
    $warehouse = Warehouse::factory()->create();
    $staff = User::factory()->warehouseStaff()->create(['warehouse_id' => $warehouse->id]);
    $shipment = \App\Models\Shipment::factory()->create([
        'current_warehouse_id' => $warehouse->id,
    ]);

    $this->actingAs($staff, 'sanctum')
        ->patchJson("/api/v1/shipments/{$shipment->id}/status", ['status' => 'delivered'])
        ->assertForbidden();
});