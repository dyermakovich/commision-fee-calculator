<?php

declare(strict_types=1);

namespace DY\CFC\Config;

interface ConfigInterface
{
    public function getDepositCommission(): float;

    public function getWithdrawPrivateCommission(): float;

    public function getWithdrawPrivateFreeOfChargeAmount(): float;

    public function getBaseCurrency(): string;

    public function getWithdrawPrivateFreeOfChargeCount(): int;

    public function getWithdrawBusinessCommission(): float;
}
