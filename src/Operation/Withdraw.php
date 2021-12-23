<?php

declare(strict_types=1);

namespace DY\CFC\Operation;

use DY\CFC\Exception\UnexpectedException;

class Withdraw extends OperationAbstract
{
    private const MAX_FREE_OF_CHARGE_WITHDRAW_AMOUNT_PER_WEEK_IN_EUR = 1000;
    private const MAX_FREE_OF_CHARGE_WITHDRAW_COUNT_PER_WEEK = 3;

    public function getMaxFreeOfChargeWithdrawAmountPerWeekInEuro(): int
    {
        return self::MAX_FREE_OF_CHARGE_WITHDRAW_AMOUNT_PER_WEEK_IN_EUR;
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
    public function getWithdrawAmountDuringThisWeekInEuro(): float
    {
        return $this->getOperationsAmountDuringThisWeekInEuro(OperationType::WITHDRAW);
    }

    /**
     * @throws UnexpectedException
     */
    public function getAmountForCharge(): float
    {
        if ($this->getUser()->isBusiness()) {
            return $this->getAmount();
        }

        if ($this->getWithdrawCountDuringThisWeek() >= self::MAX_FREE_OF_CHARGE_WITHDRAW_COUNT_PER_WEEK) {
            return $this->getAmount();
        }

        $amountFreeOfChargeInEuro = $this->getMaxFreeOfChargeWithdrawAmountPerWeekInEuro() - $this->getWithdrawAmountDuringThisWeekInEuro();

        if ($amountFreeOfChargeInEuro <= 0) {
            return $this->getAmount();
        }

        if ($this->getAmountInEuro() <= $amountFreeOfChargeInEuro) {
            return 0;
        }

        return $this->getCurrency()->convertFromEuro($this->getAmountInEuro() - $amountFreeOfChargeInEuro);
    }

    public function getCommissionRate(): float
    {
        return $this->getUser()->isBusiness() ? 0.005 : 0.003;
    }
}
