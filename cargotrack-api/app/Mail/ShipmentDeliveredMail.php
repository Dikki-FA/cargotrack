<?php

namespace App\Mail;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
//use Illuminate\Mail\Mailables\Attachment;
//use Illuminate\Mail\Mailables\Content;
//use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShipmentDeliveredMail extends Mailable implements ShouldQueue 
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Shipment $shipment)
    {
        //
    }

    public function build()
    {
        return $this
            ->subject("Shipment {$this->shipment->tracking_number} telah terkirim")
            ->view('emails.shipment-delivered');
    }


}
