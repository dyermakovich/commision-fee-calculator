<?php

declare(strict_types=1);

namespace DY\CFC\Operation;

use DateTimeInterface;
use DY\CFC\Currency\CurrencyInterface;
use DY\CFC\User\UserInterface;

interface OperationInterface extends RounderAwareInterface, PreviousWeekOperationsInterface
{
    public function getDate(): DateTimeInterface;

    public function getUser(): UserInterface;

    public function isDeposit(): bool;

    public function getAmount(): float;

    public function getAmountInBaseCurrency(): float;

    public function getAmountForCharge(): float;

    public function getCommissionRate(): float;

    public function getCurrency(): CurrencyInterface;

    public function getFee(): float;

    public function getPrevious(): ?OperationInterface;

    public function getType(): string;
}
