<?php

declare(strict_types=1);

namespace DY\CFC\Service;

interface RounderInterface
{
    public function roundUp(float $amount, int $precision = 0): float;
}
