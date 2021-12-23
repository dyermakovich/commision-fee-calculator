<?php

namespace DY\CFC\Operation;

interface PreviousWeekOperationsInterface
{
    public function getOperationsDuringThisWeek(string $type = OperationType::DEPOSIT): array;

    public function getOperationsCountDuringThisWeek(string $type = OperationType::DEPOSIT): int;

    public function getOperationsAmountDuringThisWeekInEuro(string $type = OperationType::DEPOSIT): float;
}
