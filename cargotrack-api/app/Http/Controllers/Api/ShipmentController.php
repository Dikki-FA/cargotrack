<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\EstimateShippingRequest;
use App\Http\Requests\UpdateShipmentStatusRequest;
use App\Models\Shipment;
use App\Services\ShippingCalculatorService;
use App\Services\TrackingNumberGenerator;
use App\Services\QrCodeGenerator;
use App\Mail\ShipmentDeliveredMail;
use App\Events\ShipmentStatusUpdated;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

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

    public function store(
        StoreShipmentRequest $request, 
        ShippingCalculatorService $calculator,
        TrackingNumberGenerator $trackingNumber)
    {
        $data = $request->validated();

        $estimate = $calculator->calculate(
            $data['origin_city'], 
            $data['destination_city'], 
            (float) $data['weight_kg']);

        $shipment = DB::transaction(function () use ($data, $estimate, $trackingNumber, $request) {
            $shipment = Shipment::create([
                ...$data,
                'tracking_number' => $trackingNumber->generate(),
                'shipping_cost' => $estimate['total_cost'],
                'current_status' => 'pending',
            ]);

            $shipment->logs()->create([
                'status' => 'pending',
                'location_description' => $data['origin_city'],
                'notes' => 'Order dibuat.',
                'created_by_user_id' => $request->user()->id,
            ]);

            return $shipment;
        });

        return response()->json([
            'data' => $shipment->fresh(),
            'message' => 'Shipment berhasil dibuat.',
            ], 201);
    }

    public function estimate(
        EstimateShippingRequest $request, 
        ShippingCalculatorService $calculator)
    {
        $data = $request->validated();

        $estimate = $calculator->calculate(
            $data['origin_city'], 
            $data['destination_city'], 
            (float) $data['weight_kg']);

        return response()->json(['data' => $estimate]);
    }

    public function updateStatus(UpdateShipmentStatusRequest $request, Shipment $shipment)
    {
        $data = $request->validated();
        
        Gate::authorize('updateStatus', [$shipment, $data['status']]);

        $log = DB::transaction(function () use ($shipment, $data, $request) {
            $shipment->update(['current_status' => $data['status']]);
        
            
            return $shipment->logs()->create([
                'status' => $data['status'],
                'location_description' => $data['location_description'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by_user_id' => $request->user()->id,
            ]);
        });
                
        event(new ShipmentStatusUpdated($shipment, $log));

        if ($data['status'] === 'delivered') {
            $creatingAdmin = $shipment->logs()
                ->where('status', 'pending')
                ->first()
                ?->creator;
            
            if ($creatingAdmin) {
                Mail::to($creatingAdmin->email)->queue(new ShipmentDeliveredMail($shipment));
            }
        }

        return response()->json([
            'data' => [
                'shipment' => $shipment->fresh(),
                'log' => $log
            ],
            'message' => 'Status berhasil diperbarui.',
        ]);
    }

    public function label(Shipment $shipment, QrCodeGenerator $qr)
    {
        Gate::authorize('downloadLabel', $shipment);

        $pdf = Pdf::loadView('labels.shipment', [
            'shipment' => $shipment,
            'qrDataUri' => $qr->svgDataUri($shipment->tracking_number),
        ])->setPaper([0, 0, 283.46, 425.2]); // 100*150 mm

        return $pdf->download("label-{$shipment->tracking_number}.pdf");
    }
    
}
