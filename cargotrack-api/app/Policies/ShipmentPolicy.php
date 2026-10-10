<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;
//use Illuminate\Auth\Access\Response;

class ShipmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'warehouse_staff', 'courier']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Shipment $shipment): bool
    {
        return match ($user->role) {
            'admin' => true,
            'warehouse_staff' => $this->ownsWarehouse($user, $shipment),
            'courier' => $this->isAssignedCourier($user, $shipment),
            default => false,
        };
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateStatus(User $user, Shipment $shipment, string $newStatus): bool
    {
        return match ($user->role) {
            'admin' => true,
            'warehouse_staff' => $this->ownsWarehouse($user, $shipment) 
                && in_array($newStatus, ['received_at_warehouse', 'in_transit']),
            'courier' => $this->isAssignedCourier($user, $shipment) 
                && in_array($newStatus, ['out_for_delivery', 'delivered']),
            default => false,
        };
    }

    public function downloadLabel(User $user, Shipment $shipment): bool
    {
        return $this->view ($user, $shipment);
    }

    public function ownsWarehouse(User $user, Shipment $shipment): bool
    {
        return $this->view ($user, $shipment);
    }

    public function isAssignedCourier(User $user, Shipment $shipment): bool
    {
        $courierId = $user->courier?->id;

        return $courierId !== null 
            && $shipment->assigned_courier_id === $courierId;
    }

/**
    *public function delete(User $user, Shipment $shipment): bool
    *{
    *    return false;
    *}

    *public function restore(User $user, Shipment $shipment): bool
    *{
    *    return false;
    *}

    *public function forceDelete(User $user, Shipment $shipment): bool
    *{
    *    return false;
    *}
*/
    public function courier()
    {
        return $this->hasOne(\App\Models\Courier::class);
    }

}
