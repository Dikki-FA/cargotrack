<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label {{ $shipment->tracking_number }}</title>
    <style>
        @page { margin: 8mm;}
        body { font-family: DejaVu Sans, sans-serif; font-size: 8pt; color: #000; }
        .header { text-align: center; border-bottom: 1px solid #000; padding-bottom: 4px; }
        .brand { font-size: 12pt; font-weight: bold; }
        .qr { text-align: center; margin: 8px 0 2px; }
        .qr img { width: 110px; height: 110px; }
        .tracking { text-align: center; font-size: 10pt; font-weight: bold; margin-bottom: 8px; }
        .box { border: 1px solid #000; padding: 4px 6px; margin-bottom: 6px; }
        .label { font-size: 6.5pt; text-transform: uppercase; color: #444; }
        .name { font-weight: bold; font-size: 9pt; }
        table.meta { width: 100%; border-collapse: collapse; }
        table.meta td { border: 1px solid #000; padding: 3px 5px; width: 33%; vertical-align: top; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">CargoTrack</div>
    </div>

    <div class="qr">
        <img src="{{ $qrDataUri }}" alt="QR">
    </div>
    <div class="tracking">{{ $shipment->tracking_number }}</div>

    <div class="box">
        <div class="label">Penerima</div>
        <div class="name">{{ $shipment->receiver_name }}</div>
        <div>{{ $shipment->receiver_phone }}</div>
        <div>{!! nl2br(e($shipment->receiver_address)) !!}</div>
        <div><strong>{{ $shipment->destination_city}}</strong></div>
    </div>

    <div class="box">
        <div class="label">Pengirim</div>
        <div class="name">{{ $shipment->sender_name }}</div>
        <div>{{ $shipment->sender_phone }}</div>
        <div>{!! nl2br(e($shipment->sender_address)) !!}</div>
        <div><strong>{{ $shipment->origin_city}}</strong></div>
    </div>

    <table class="meta">
        <tr>
            <td><div class="label">Berat</div>{{ rtrim(rtrim(number_format((float) $shipment->weight_kg, 2, '.', ''), '0'), '.') }} kg</td>
            <td><div class="label">Jenis Barang</div>{{ $shipment->item_type }}</td>
            <td><div class="label">Biaya</div>{{ number_format((float) $shipment->shipping_cost, 0, ',', '.') }}</td>
        </tr>
    </table>
</body>
</html>