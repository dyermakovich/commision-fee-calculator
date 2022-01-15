<?php

declare(strict_types=1);

namespace DY\CFC\Operation;

use DY\CFC\Exception\UnexpectedException;

final class Withdraw implements OperationInterface
{
    use OperationTrait;

    public function getMaxFreeOfChargeWithdrawAmountPerWeekInBaseCurrency(): float
    {
        return $this->config->getWithdrawPrivateFreeOfChargeAmount();
    }

    /**
     * @throws UnexpectedException
     */
    public function getWithdrawCountDuringThisWeek(): int
    {
        return $this->getOperationsCountDuringThisWeek(OperationType::WITHDRAW);
    }

    /**
     * @throws UnexpectedException
     */
    public function getWithdrawAmountDuringThisWeekInBaseCurrency(): float
    {
        return $this->getOperationsAmountDuringThisWeekInBaseCurrency(OperationType::WITHDRAW);
    }

    /**
     * @throws UnexpectedException
     */
    public function getAmountForCharge(): float
    {
        if ($this->getUser()->isBusiness()) {
            return $this->getAmount();
        }

        if ($this->getWithdrawCountDuringThisWeek() >= $this->config->getWithdrawPrivateFreeOfChargeCount()) {
            return $this->getAmount();
        }

        $amountFreeOfChargeInBaseCurrency = $this->getMaxFreeOfChargeWithdrawAmountPerWeekInBaseCurrency()
            - $this->getWithdrawAmountDuringThisWeekInBaseCurrency();

        if ($amountFreeOfChargeInBaseCurrency <= 0) {
            return $this->getAmount();
        }

        if ($this->getAmountInBaseCurrency() <= $amountFreeOfChargeInBaseCurrency) {
            return 0;
        }

        return $this->getCurrency()->convertFromBaseCurrency(
            $this->getAmountInBaseCurrency() - $amountFreeOfChargeInBaseCurrency
        );
    }

    public function getCommissionRate(): float
    {
        return $this->getUser()->isBusiness()
            ? $this->config->getWithdrawBusinessCommission()
            : $this->config->getWithdrawPrivateCommission();
    }
}
