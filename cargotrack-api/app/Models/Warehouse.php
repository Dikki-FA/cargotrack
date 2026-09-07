<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'address',
        'city',
        'phone',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'current_warehouse_id');
    }
}
