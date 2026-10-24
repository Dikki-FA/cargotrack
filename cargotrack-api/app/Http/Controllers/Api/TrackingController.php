<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
//use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function show(string $tracking_number)
    {
        $shipment = Shipment::with(['logs' => fn ($q) => $q->orderBy('created_at')->orderBy('id')])
        ->where('tracking_number', strtoupper(trim($tracking_number)))->first();

        if (!$shipment) {
            return response()->json([
                'message' => 'Nomor resi tidak ditemukan.'], 404);
        }

        return response()->json([
            'data' => [
                'tracking_number' => $shipment->tracking_number,
                'current_status' => $shipment->current_status,
                'origin_city' => $shipment->origin_city,
                'destination_city' => $shipment->destination_city,
                'item_type' => $shipment->item_type,
                'weight_kg' => $shipment->weight_kg,
                'logs' => $shipment->logs->map(fn ($log) => [
                    'status' => $log->status,
                    'location_description' => $log->location_description,
                    'notes' => $log->notes,
                    'created_at' => $log->created_at,
                ])->values(),
            ],
        ]);
    }
}
