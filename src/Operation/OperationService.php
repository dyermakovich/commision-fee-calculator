<?php

declare(strict_types=1);

namespace DY\CFC\Operation;

use DY\CFC\Currency\CurrencyInterface;
use DY\CFC\Operation\Exception\WrongOperationAmountException;
use DY\CFC\Operation\Exception\WrongOperationDateException;
use DY\CFC\Operation\Exception\WrongOperationTypeException;
use DY\CFC\Service\Parser;
use DY\CFC\Service\ParserInterface;
use DY\CFC\User\UserInterface;

class OperationService implements OperationServiceInterface
{
    public function __construct(private ParserInterface $parser)
    {
    }

    public static function create(): OperationServiceInterface
    {
        return new OperationService(Parser::create());
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

        $operation = OperationAbstract::create(
            $operationDate,
            $type,
            $operationAmount,
            $currency,
            $user
        );

        $user->addOperation($operation);

        return $operation;
    }
}
