<?php

declare(strict_types=1);

namespace DY\CFC\Operation\Strategy;

use DY\CFC\Config\ConfigInterface;
use DY\CFC\Operation\OperationInterface;
use DY\CFC\Operation\OperationType;

final class DepositStrategy implements OperationStrategyInterface
{
    public function __construct(
        private OperationInterface $operation,
        private ConfigInterface $config
    ) {
    }

    public function getAmountForCharge(): float
    {
        return $this->operation->getAmount();
    }

    public function getCommissionRate(): float
    {
        return $this->config->getDepositCommission();
    }

    public function getType(): string
    {
        return OperationType::DEPOSIT;
    }
}
