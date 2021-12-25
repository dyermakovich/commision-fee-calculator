<?php

declare(strict_types=1);

namespace DY\CFC\Operation;

use DateTime;
use DateTimeInterface;
use DY\CFC\Currency\CurrencyInterface;
use DY\CFC\Exception\UnexpectedException;
use DY\CFC\Operation\Exception\WrongOperationTypeException;
use DY\CFC\Service\RounderInterface;
use DY\CFC\User\UserInterface;

abstract class OperationAbstract implements OperationInterface
{
    private ?OperationInterface $previous;
    private ?RounderInterface $rounder;

    private function __construct(
        private DateTimeInterface $date,
        private string $type,
        private float $amount,
        private CurrencyInterface $currency,
        private UserInterface $user
    ) {
        $this->previous = $user->getLastOperation();
    }

    /**
     * @throws WrongOperationTypeException
     */
    public static function create(
        DateTimeInterface $date,
        string $type,
        float $amount,
        CurrencyInterface $currency,
        UserInterface $user
    ): OperationInterface {
        if ($type === OperationType::DEPOSIT) {
            return new Deposit($date, $type, $amount, $currency, $user);
        }
        if ($type === OperationType::WITHDRAW) {
            return new Withdraw($date, $type, $amount, $currency, $user);
        }

        throw new WrongOperationTypeException();
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function isDeposit(): bool
    {
        return $this->getType() === OperationType::DEPOSIT;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getAmountInEuro(): float
    {
        return $this->currency->convertToEuro($this->getAmount());
    }

    public function getCurrency(): CurrencyInterface
    {
        return $this->currency;
    }

    public function getFee(): float
    {
        return $this->rounder->roundUp(
            $this->getAmountForCharge() * $this->getCommissionRate(),
            $this->currency->getPrecision()
        );
    }

    public function getPrevious(): ?OperationInterface
    {
        return $this->previous;
    }

    public function setRounder(RounderInterface $rounder)
    {
        $this->rounder = $rounder;
    }

    /**
     * @throws UnexpectedException
     */
    public function getTheNearestMonday(): DateTimeInterface
    {
        if ($this->getDate()->format('N') === '1') {
            return $this->getDate();
        }

        $date = DateTime::createFromInterface($this->getDate())->modify("previous monday");

        if ($date === false) {
            throw new UnexpectedException();
        }

        return $date;
    }

    /**
     * @throws UnexpectedException
     */
    public function getTheNearestMondayAsString(): string
    {
        return $this->getTheNearestMonday()->format("Y-m-d");
    }

    /**
     * @return OperationInterface[]
     * @throws UnexpectedException
     */
    public function getOperationsDuringThisWeek(string $type = OperationType::DEPOSIT): array
    {
        $result = array();

        $theNearestMonday = $this->getTheNearestMonday();
        $currentOperation = $this->getPrevious();

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

    /**
     * Returns number of operations from the nearest Monday before current operation.
     * @throws UnexpectedException
     */
    public function getOperationsCountDuringThisWeek(string $type = OperationType::DEPOSIT): int
    {
        return count($this->getOperationsDuringThisWeek($type));
    }

    /**
     * Returns amount of operations from the nearest Monday before current operation.
     * @throws UnexpectedException
     */
    public function getOperationsAmountDuringThisWeekInEuro(string $type = OperationType::DEPOSIT): float
    {
        $amount = 0;

        foreach ($this->getOperationsDuringThisWeek($type) as $operation) {
            $amount += $operation->getAmountInEuro();
        }

        return $amount;
    }
}
