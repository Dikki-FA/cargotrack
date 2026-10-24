<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipment Delivered</title>
</head>
<body>
    <p>Shipment dengan nomor resi <strong>{{ $shipment->tracking_number }}</strong> telah berhasil terkirim ke penerima.</p>
    <table>
        <tr>
            <td>Penerima</td>
            <td>{{ $shipment->receiver_name }}</td>
        </tr>
        <tr>
            <td>Kota tujuan</td>
            <td>{{ $shipment->destination_city }}</td>
        </tr>
        <tr>
            <td>Jenis barang</td>
            <td>{{ $shipment->item_type }}</td>
        </tr>
    </table>
    <p>(Email ini adalah simulasi notifikasi untuk admin yang membuat order.)</p>
</body>
</html>