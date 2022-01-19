<?php

declare(strict_types=1);

namespace DY\CFC\Operation\Factory;

use DY\CFC\Config\ConfigInterface;
use DY\CFC\Currency\CurrencyInterface;
use DY\CFC\Operation\Exception\WrongOperationTypeException;
use DY\CFC\Service\RounderInterface;
use DY\CFC\User\UserInterface;
use DateTimeInterface;
use DY\CFC\Operation\OperationInterface;
use DY\CFC\Operation\Operation;

class OperationFactory
{
    /**
     * @throws WrongOperationTypeException
     */
    public static function create(
        DateTimeInterface $date,
        string $type,
        float $amount,
        CurrencyInterface $currency,
        UserInterface $user,
        ConfigInterface $config,
        RounderInterface $rounder
    ): OperationInterface {
        $operation = new Operation($date, $amount, $currency, $user, $rounder);

        $strategy = OperationStrategyFactory::create($type, $config, $operation);
        $feeStrategy = FeeStrategyFactory::create($operation, $rounder);

        $operation->setStrategy($strategy)
            ->setFeeStrategy($feeStrategy);

        return $operation;
    }
}
