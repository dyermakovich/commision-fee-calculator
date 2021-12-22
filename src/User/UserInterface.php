<?php

declare(strict_types=1);

namespace DY\CFC\User;

use DY\CFC\Operation\OperationInterface;

interface UserInterface
{
    public function getID(): int;

    public function isBusiness(): bool;

    public function addOperation(OperationInterface $operation): void;

    public function getLastOperation(): ?OperationInterface;
}
