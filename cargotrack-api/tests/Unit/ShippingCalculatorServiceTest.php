<?php

use App\Services\ShippingCalculatorService;

it('menghitung tarif antar kota dengan benar', function () {
    $result = (new ShippingCalculatorService())->calculate('Jakarta', 'Bandung', 2.5);

    expect($result['base_fee'])->toBe(10000)
        ->and($result['weight_cost'])->toBe(12500.0)
        ->and($result['intercity_surcharge'])->toBe(5000)
        ->and($result['total_cost'])->toBe(27500.0);
});

it('tidak menambah surcharge jika kota asal dan tujuan sama', function () {
    $result = (new ShippingCalculatorService())->calculate('Jakarta', 'Jakarta', 2.5);

    expect($result['intercity_surcharge'])->toBe(0)
        ->and($result['total_cost'])->toBe(22500.0);
});

it('mengabaikan perbedaan huruf besar/kecil dan spasi pada nama kota', function () {
    $result = (new ShippingCalculatorService())->calculate(' jakarta ', 'JAKARTA', 1.0);

    expect($result['intercity_surcharge'])->toBe(0);
});

it('menghitung ulang dengan benar untuk berat yang berbeda', function (float $weight, float $expectedTotal) {
    $result = (new ShippingCalculatorService())->calculate('Jakarta', 'Bandung', $weight);

    expect($result['total_cost'])->toBe($expectedTotal);
})->with([
    [1.0, 20000.0],
    [0.1, 15500.0],
    [10.0, 65000.0],
]);