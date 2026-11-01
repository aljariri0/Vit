
<?php

class ShippingCalculator
{
    private const RATES = [
        "amman"   => 2.82,
        "zarqa"   => 3.53,
        "irbid"   => 4.23,
        "balqa"   => 3.53,
        "madaba"  => 4.23,
        "jerash"  => 4.94,
        "ajloun"  => 4.94,
        "mafraq"  => 5.64,
        "karak"   => 5.64,
        "tafilah" => 6.35,
        "maan"    => 7.05,
        "aqaba"   => 7.05
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
