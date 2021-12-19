<?php

declare(strict_types=1);

namespace DY\CFC\Operation;

use DY\CFC\User\UserInterface;

interface OperationServiceInterface
{
    public function addNew(
        string $date,
        string $type,
        string $amount,
        string $currency,
        UserInterface $user
    ): OperationInterface;

    /**
     * @return OperationInterface[]
     */
    public function getAll(): array;
}
