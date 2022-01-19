<?php

declare(strict_types=1);

namespace DY\CFC\Operation\Factory;

use DY\CFC\Config\ConfigInterface;
use DY\CFC\Operation\Exception\WrongOperationTypeException;
use DY\CFC\Operation\OperationInterface;
use DY\CFC\Operation\OperationType;
use DY\CFC\Operation\Strategy\DepositStrategy;
use DY\CFC\Operation\Strategy\OperationStrategyInterface;
use DY\CFC\Operation\Strategy\SearchOperationStrategy;
use DY\CFC\Operation\Strategy\WithdrawStrategy;

class OperationStrategyFactory
{
    /**
     * @throws WrongOperationTypeException
     */
    public static function create(
        string $type,
        ConfigInterface $config,
        OperationInterface $operation
    ): OperationStrategyInterface {
        if ($type === OperationType::DEPOSIT) {
            return new DepositStrategy($operation, $config);
        }

        if ($type === OperationType::WITHDRAW) {
            $searchStrategy = new SearchOperationStrategy();
            return new WithdrawStrategy($operation, $config, $searchStrategy);
        }

        throw new WrongOperationTypeException();
    }
}
