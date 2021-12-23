<?php

declare(strict_types=1);

namespace DY\CFC\Service;

class Rounder implements RounderInterface
{
    public static function create(): RounderInterface
    {
        return new Rounder();
    }

    public function roundUp(float $amount, int $precision = 0): float
    {
        $tenToPrecisionPower = 10 ** $precision;
        return ceil($amount * $tenToPrecisionPower) / $tenToPrecisionPower;
    }
}
