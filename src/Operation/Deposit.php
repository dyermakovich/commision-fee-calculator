<?php

declare(strict_types=1);

namespace DY\CFC\Operation;

final class Deposit implements OperationInterface
{
    use OperationTrait;
    
    public function getAmountForCharge(): float
    {
        return $this->getAmount();
    }

    public function getCommissionRate(): float
    {
        return $this->config->getDepositCommission();
    }
}
