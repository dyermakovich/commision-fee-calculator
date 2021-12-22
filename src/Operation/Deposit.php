<?php

declare(strict_types=1);

namespace DY\CFC\Operation;

use DateTimeInterface;
use DY\CFC\Currency\CurrencyInterface;
use DY\CFC\User\UserInterface;

class Deposit extends OperationAbstract
{
    public function __construct(DateTimeInterface $date, float $amount, CurrencyInterface $currency, UserInterface $user)
    {
        parent::__construct($date, self::DEPOSIT, $amount, $currency, $user);
    }

    public function getAmountForCharge(): float
    {
        return $this->getAmount();
    }

    public function getCommissionRate(): float
    {
        return 0.0003;
    }
}
