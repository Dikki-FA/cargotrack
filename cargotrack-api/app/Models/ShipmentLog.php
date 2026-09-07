<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentLog extends Model
{
    //
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'shipment_id',
        'status',
        'location_description',
        'notes',
        'created_by_user_id',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
