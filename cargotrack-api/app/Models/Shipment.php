<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'sender_name',
        'sender_phone',
        'sender_address',
        'origin_city',
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        'destination_city',
        'weight_kg',
        'item_type',
        'current_status',
        'shipping_cost',
        'current_warehouse_id',
        'assigned_courrier_id',
    ];

    public function warehouse()
    {
        return $this->belongTo(Warehouse::class, 'current_warehouse_id');
    }

    public function courier()
    {
        return $this->belongTo(Courier::class, 'assigned_courier_id');
    }

    public function logs()
    {
        return $this->hasMany(ShipmentLog::class);
    }
}
