<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Shipment::class);

        $user = $request->user();

        $shipments = Shipment::query()
            ->when($user->role === 'warehouse_staff', function ($q) use ($user)
            {
                user->warehouse_id
                    ? $q->where('current_warehouse_id', $user->warehouse_id)
                    : $q->whereRaw('1=0'); // If the user is a warehouse staff but not assigned to any warehouse, return no shipments
            })

            ->when($user->role === 'courier', function ($q) use ($user) {
                $courierId = $user->courier?->id;
                $courierId
                    ? $q->where('assigned_courier_id', $courierId)
                    : $q->whereRaw('1=0'); // If the user is a courier but not assigned to any courier, return no shipments
            })
            
            ->when(request()->filled('search'), function ($q) {
                $term = $request->search;
                $q->where(function ($q) use ($term) {
                    $q->where('tracking_number', 'ilike', "%{$term}%")
                        ->orWhere('sender_name', 'ilike', "%{$term}%")
                        ->orWhere('receiver_name', 'ilike', "%{$term}%");
                });
            })

            ->when($request->filled('status'), fn ($q) => $q->where('current_status', $request->status))
            ->latest()
            ->paginate(min($request->integer('per_page', 15), 100));
        
        return response()->json($shipments);
    }
}
