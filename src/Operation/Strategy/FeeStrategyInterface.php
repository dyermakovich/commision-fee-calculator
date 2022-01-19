<?php

declare(strict_types=1);

namespace DY\CFC\Operation\Strategy;

interface FeeStrategyInterface
{
    public function getFee(): float;
}
