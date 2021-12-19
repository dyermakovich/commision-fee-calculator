<?php

declare(strict_types=1);

namespace DY\CFC\Operation;

use DateTimeInterface;
use DY\CFC\User\UserInterface;

interface OperationInterface
{
    public function getDate(): DateTimeInterface;

    public function getUser(): UserInterface;

    public function isDeposit(): bool;

    public function getAmount(): float;

    public function getCurrency(): string;

    public function getFee(): float;
}
