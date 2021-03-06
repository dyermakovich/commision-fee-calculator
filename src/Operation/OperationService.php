<?php

declare(strict_types=1);

namespace DY\CFC\Operation;

use DY\CFC\Config\Config;
use DY\CFC\Config\ConfigInterface;
use DY\CFC\Currency\CurrencyInterface;
use DY\CFC\Operation\Exception\WrongOperationAmountException;
use DY\CFC\Operation\Exception\WrongOperationDateException;
use DY\CFC\Operation\Exception\WrongOperationTypeException;
use DY\CFC\Operation\Factory\OperationFactory;
use DY\CFC\Service\Parser;
use DY\CFC\Service\ParserInterface;
use DY\CFC\Service\Rounder;
use DY\CFC\Service\RounderInterface;
use DY\CFC\User\UserInterface;

class OperationService implements OperationServiceInterface
{
    public function __construct(
        private ParserInterface $parser,
        private RounderInterface $rounder,
        private ConfigInterface $config
    ) {
    }

    public static function create(): OperationServiceInterface
    {
        return new OperationService(Parser::create(), Rounder::create(), Config::create());
    }

    /**
     * @throws WrongOperationDateException
     * @throws WrongOperationAmountException
     * @throws WrongOperationTypeException
     */
    public function addNew(
        string $date,
        string $type,
        string $amount,
        CurrencyInterface $currency,
        UserInterface $user
    ): OperationInterface {
        $operationDate = $this->parser->parseDate($date);

        if (!isset($operationDate)) {
            throw new WrongOperationDateException();
        }

        $operationAmount = $this->parser->parseFloat($amount);

        if (!isset($operationAmount)) {
            throw new WrongOperationAmountException();
        }

        $operation = OperationFactory::create(
            $operationDate,
            $type,
            $operationAmount,
            $currency,
            $user,
            $this->config,
            $this->rounder
        );

        $user->addOperation($operation);

        return $operation;
    }
}
