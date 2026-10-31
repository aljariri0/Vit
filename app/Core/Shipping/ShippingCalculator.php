
<?php

class ShippingCalculator
{
    private const RATES = [
        "amman"   => 2.00,
        "zarqa"   => 2.50,
        "irbid"   => 3.00,
        "balqa"   => 2.50,
        "madaba"  => 3.00,
        "jerash"  => 3.50,
        "ajloun"  => 3.50,
        "mafraq"  => 4.00,
        "karak"   => 4.00,
        "tafilah" => 4.50,
        "maan"    => 5.00,
        "aqaba"   => 5.00
    ];

    public function calculate(string $governorate): float
    {
        $governorate = strtolower(trim($governorate));

        if (!array_key_exists($governorate, self::RATES)) {
            throw new InvalidArgumentException(
                "Invalid governorate"
            );
        }

        return self::RATES[$governorate];
    }
}
