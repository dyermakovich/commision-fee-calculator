<?php

declare(strict_types=1);

namespace DY\CFC\Operation\Strategy;

use DY\CFC\Config\ConfigInterface;
use DY\CFC\Exception\UnexpectedException;
use DY\CFC\Operation\OperationInterface;
use DY\CFC\Operation\OperationType;

final class WithdrawStrategy implements OperationStrategyInterface
{
    public function __construct(
        private OperationInterface $operation,
        private ConfigInterface $config,
        private SearchOperationStrategyInterface $searchOperationStrategy
    ) {
    }

    private function getMaxFreeOfChargeWithdrawAmountPerWeekInBaseCurrency(): float
    {
        return $this->config->getWithdrawPrivateFreeOfChargeAmount();
    }

    /**
     * @throws UnexpectedException
     */
    private function getWithdrawCountDuringThisWeek(): int
    {
        return $this->searchOperationStrategy->getOperationsCountDuringThisWeek(
            $this->operation,
            OperationType::WITHDRAW
        );
    }

    /**
     * @throws UnexpectedException
     */
    private function getWithdrawAmountDuringThisWeekInBaseCurrency(): float
    {
        return $this->searchOperationStrategy->getOperationsAmountDuringThisWeekInBaseCurrency(
            $this->operation,
            OperationType::WITHDRAW
        );
    }

    /**
     * @throws UnexpectedException
     */
    public function getAmountForCharge(): float
    {
        $operation = $this->operation;
        $user = $operation->getUser();

        if ($user->isBusiness()) {
            return $operation->getAmount();
        }

        if ($this->getWithdrawCountDuringThisWeek() >= $this->config->getWithdrawPrivateFreeOfChargeCount()) {
            return $operation->getAmount();
        }

        $amountFreeOfChargeInBaseCurrency = $this->getMaxFreeOfChargeWithdrawAmountPerWeekInBaseCurrency()
            - $this->getWithdrawAmountDuringThisWeekInBaseCurrency();

        if ($amountFreeOfChargeInBaseCurrency <= 0) {
            return $operation->getAmount();
        }

        if ($operation->getAmountInBaseCurrency() <= $amountFreeOfChargeInBaseCurrency) {
            return 0;
        }

        return $operation->getCurrency()->convertFromBaseCurrency(
            $operation->getAmountInBaseCurrency() - $amountFreeOfChargeInBaseCurrency
        );
    }

    public function getCommissionRate(): float
    {
        return $this->operation->getUser()->isBusiness()
            ? $this->config->getWithdrawBusinessCommission()
            : $this->config->getWithdrawPrivateCommission();
    }

    public function getType(): string
    {
        return OperationType::WITHDRAW;
    }
}
