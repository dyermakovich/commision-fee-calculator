<?php

declare(strict_types=1);

namespace DY\CFC\Operation\Strategy;

interface OperationStrategyInterface
{
    public function getAmountForCharge(): float;

    public function getCommissionRate(): float;

    public function getType(): string;
}
