<?php

declare(strict_types=1);

namespace DY\CFC\Operation\Strategy;

use DY\CFC\Exception\UnexpectedException;
use DateTimeInterface;
use DY\CFC\Operation\OperationInterface;
use DY\CFC\Operation\OperationType;

interface SearchOperationStrategyInterface
{
    /**
     * @throws UnexpectedException
     */
    public function getTheNearestMonday(OperationInterface $operation): DateTimeInterface;

    /**
     * @throws UnexpectedException
     */
    public function getTheNearestMondayAsString(OperationInterface $operation): string;

    /**
     * @return OperationInterface[]
     * @throws UnexpectedException
     */
    public function getOperationsDuringThisWeek(
        OperationInterface $operation,
        string $type = OperationType::DEPOSIT
    ): array;

    /**
     * Returns number of operations from the nearest Monday before current operation.
     * @throws UnexpectedException
     */
    public function getOperationsCountDuringThisWeek(
        OperationInterface $operation,
        string $type = OperationType::DEPOSIT
    ): int;

    /**
     * Returns amount of operations from the nearest Monday before current operation.
     * @throws UnexpectedException
     */
    public function getOperationsAmountDuringThisWeekInBaseCurrency(
        OperationInterface $operation,
        string $type = OperationType::DEPOSIT
    ): float;

}
