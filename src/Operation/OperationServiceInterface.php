<?php

declare(strict_types=1);

namespace DY\CFC\Operation;

use DY\CFC\Currency\CurrencyInterface;
use DY\CFC\User\UserInterface;

interface OperationServiceInterface
{
    public function addNew(
        string $date,
        string $type,
        string $amount,
        CurrencyInterface $currency,
        UserInterface $user
    ): OperationInterface;
}
