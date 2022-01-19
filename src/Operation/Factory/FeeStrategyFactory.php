<?php

declare(strict_types=1);

namespace DY\CFC\Operation\Factory;

use DY\CFC\Operation\OperationInterface;
use DY\CFC\Operation\Strategy\FeeStrategy;
use DY\CFC\Operation\Strategy\FeeStrategyInterface;
use DY\CFC\Service\RounderInterface;

class FeeStrategyFactory
{
    public static function create(OperationInterface $operation, RounderInterface $rounder): FeeStrategyInterface
    {
        return new FeeStrategy($operation, $rounder);
    }
}
