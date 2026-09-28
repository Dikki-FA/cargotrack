<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_type',
        'license_plate',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'assigned_courier_id');
    }
}
