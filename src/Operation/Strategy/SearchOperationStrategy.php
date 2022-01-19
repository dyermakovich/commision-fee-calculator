<?php

declare(strict_types=1);

namespace DY\CFC\Operation\Strategy;

use DY\CFC\Exception\UnexpectedException;
use DateTimeInterface;
use DateTime;
use DY\CFC\Operation\OperationInterface;
use DY\CFC\Operation\OperationType;

class SearchOperationStrategy implements SearchOperationStrategyInterface
{
    public function getTheNearestMonday(OperationInterface $operation): DateTimeInterface
    {
        if ($operation->getDate()->format('N') === '1') {
            return $operation->getDate();
        }

        $date = DateTime::createFromInterface($operation->getDate())
            ->modify("previous monday");

        if ($date === false) {
            throw new UnexpectedException();
        }

        return $date;
    }

    public function getOperationsDuringThisWeek(
        OperationInterface $operation,
        string $type = OperationType::DEPOSIT
    ): array {
        $result = array();

        $theNearestMonday = $this->getTheNearestMonday($operation);
        $currentOperation = $operation->getPrevious();

        while ($currentOperation !== null) {
            if ($currentOperation->getType() !== $type) {
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

    public function getOperationsCountDuringThisWeek(
        OperationInterface $operation,
        string $type = OperationType::DEPOSIT
    ): int {
        return count($this->getOperationsDuringThisWeek($operation, $type));
    }

    public function getTheNearestMondayAsString(OperationInterface $operation): string
    {
        return $this->getTheNearestMonday($operation)
            ->format("Y-m-d");
    }

    public function getOperationsAmountDuringThisWeekInBaseCurrency(
        OperationInterface $operation,
        string $type = OperationType::DEPOSIT
    ): float {
        $amount = 0;
        $operations = $this->getOperationsDuringThisWeek($operation, $type);

        foreach ($operations as $operation) {
            $amount += $operation->getAmountInBaseCurrency();
        }

        return $amount;
    }
}
