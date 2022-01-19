<?php

namespace DY\CFC\Operation\Strategy;

use DY\CFC\Operation\OperationInterface;
use DY\CFC\Service\RounderInterface;

class FeeStrategy implements FeeStrategyInterface
{
    public function __construct(
        private OperationInterface $operation,
        private RounderInterface $rounder
    ) {
    }

    public function getFee(): float
    {
        return $this->rounder->roundUp(
            $this->operation->getAmountForCharge() * $this->operation->getCommissionRate(),
            $this->operation->getCurrency()->getPrecision()
        );
    }
}
