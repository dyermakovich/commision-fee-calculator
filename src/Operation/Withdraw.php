<?php

declare(strict_types=1);

namespace DY\CFC\Operation;

use DY\CFC\Currency\CurrencyInterface;
use DY\CFC\Exception\UnexpectedException;
use DY\CFC\User\UserInterface;
use DateTimeInterface;
use DateTime;

class Withdraw extends OperationAbstract
{
    private const MAX_FREE_OF_CHARGE_WITHDRAW_AMOUNT_PER_WEEK_IN_EUR = 1000;
    private const MAX_FREE_OF_CHARGE_WITHDRAW_COUNT_PER_WEEK = 3;

    public function __construct(DateTimeInterface $date, float $amount, CurrencyInterface $currency, UserInterface $user)
    {
        parent::__construct($date, self::WITHDRAW, $amount, $currency, $user);
    }

    /**
     * @throws UnexpectedException
     */
    public function getTheNearestMonday(): DateTimeInterface
    {
        $date = DateTime::createFromInterface($this->getDate())->modify("previous monday");

        if ($date === false) {
            throw new UnexpectedException();
        }

        return $date;
    }

    /**
     * @throws UnexpectedException
     */
    public function getTheNearestMondayAsString(): string
    {
        return $this->getTheNearestMonday()->format("Y-m-d");
    }

    /**
     * @return OperationInterface[]
     * @throws UnexpectedException
     */
    public function getWithdrawsDuringThisWeek(): array
    {
        $result = array();

        $theNearestMonday = $this->getTheNearestMonday();
        $currentOperation = $this->getPrevious();

        while ($currentOperation !== null) {
            if ($currentOperation->isDeposit()) {
                $currentOperation = $currentOperation->getPrevious();
                continue;
            }

            if ($currentOperation->getDate()->getTimestamp() < $theNearestMonday->getTimestamp()) {
                break;
            }

            $result[] = $currentOperation;
            $currentOperation = $currentOperation->getPrevious();
        }

        return $result;
    }

    /**
     * Returns number of withdraw operations from the nearest Monday before current operation.
     * @throws UnexpectedException
     */
    public function getWithdrawCountDuringThisWeek(): int
    {
        return count($this->getWithdrawsDuringThisWeek());
    }

    /**
     * Returns amount of withdraw operations from the nearest Monday before current operation.
     * @throws UnexpectedException
     */
    public function getWithdrawAmountDuringThisWeek(): float
    {
        $amount = 0;

        foreach ($this->getWithdrawsDuringThisWeek() as $withdraw) {
            $amount += $withdraw->getAmount();
        }

        return $amount;
    }

    /**
     * Returns free of charge withdraw amount per week in operation currency.
     */
    public function getMaxFreeOfChargeWithdrawAmountPerWeek(): float
    {
        return $this->getCurrency()
            ->convertFromEuro(self::MAX_FREE_OF_CHARGE_WITHDRAW_AMOUNT_PER_WEEK_IN_EUR);
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

        $amountFreeOfCharge = $this->getMaxFreeOfChargeWithdrawAmountPerWeek() - $this->getWithdrawAmountDuringThisWeek();

        if ($amountFreeOfCharge <= 0) {
            return $this->getAmount();
        }

        if ($this->getAmount() <= $amountFreeOfCharge) {
            return 0;
        }

        return $this->getAmount() - $amountFreeOfCharge;
    }

    public function getCommissionRate(): float
    {
        return $this->getUser()->isBusiness() ? 0.005 : 0.003;
    }
}
