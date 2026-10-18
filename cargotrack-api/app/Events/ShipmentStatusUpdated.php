<?php

namespace App\Events;

use App\Models\Shipment;
use App\Models\ShipmentLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShipmentStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Shipment $shipment,
        public ShipmentLog $shipmentLog,
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('shipments.' . $this->shipment->tracking_number),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ShipmentStatusUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'tracking_number' => $this->shipment->tracking_number,
            'current_status' => $this->shipment->current_status,
            'log' => [
                'status' => $this->shipmentLog->status,
                'location_description' => $this->shipmentLog->location_description,
                'notes' => $this->shipmentLog->notes,
                'created_at' => $this->shipmentLog->created_at,
            ],
        ];
    }
}