<?php

namespace App\Services;

class ShippingCalculatorService
{
    public const BASE_FEE = 10000; // Base fee
    public const RATE_PER_KG = 5000; // Rate per kilogram
    public const INTERCITY_SURCHARGE = 5000; // Surcharge for intercity shipments

    public function calculate(string $originCity, string $destinationCity, float $weightKg): array
    {
        $weightCost = $weightKg * self::RATE_PER_KG;

        $surcharge = $this->isSameCity($originCity, $destinationCity) 
            ? 0 
            : self::INTERCITY_SURCHARGE;

        return [
            'base_fee' => self::BASE_FEE,
            'rate_per_kg' => self::RATE_PER_KG,
            'weight_cost' => $weightCost,
            'intercity_surcharge' => $surcharge,
            'total_cost' => self::BASE_FEE + $weightCost + $surcharge,
        ];
    }
    
    private function isSameCity(string $originCity, string $destinationCity): bool
    {
        return mb_strtolower(trim($originCity)) === mb_strtolower(trim($destinationCity));
    }
}