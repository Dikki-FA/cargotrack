<?php

namespace App\Services;

use App\Models\Shipment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;
use RuntimeException;

class TrackingNumberGenerator
{
    private const PREFIX = 'CT';
    private const MAX_ATTEMPTS = 10;

    /**
     * Wajib dipanggil di dalam DB::transaction(), dan shipment-nya.
     * harus disimpan di transaksi yang sama. Kunci baru dilepas saat transaksi selesai.
     */

    public function generate(): string
    {
        if (DB::transactionLevel() < 1){
            throw new LogicException('TrackingNumberGenerator::generate() harus dipanggil di dalam DB::transaction().');
        }

        DB::select("select pg_advisory_xact_lock(hashtext('tracking_number'))");

        $datePart = now()->format('ymd');

        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++)
            $candidate = sprintf('%s-%s-%s', self::PREFIX, $datePart, Str::upper(Str::random(4)));

        if (! Shipment::query()->where('tracking_number', $candidate)->exists()) {
            return $candidate;
        }

        throw new RuntimeException('Gagal menghasilkan nomor resi unik setelah beberapa percobaan.');
    }
}