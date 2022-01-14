<?php

declare(strict_types=1);

namespace DY\CFC\Operation;

class Deposit extends OperationAbstract
{
    public function getAmountForCharge(): float
    {
        return $this->getAmount();
    }

    public function getCommissionRate(): float
    {
        return $this->config->getDepositCommission();
    }
}
