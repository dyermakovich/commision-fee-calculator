<?php

declare(strict_types=1);

namespace DY\CFC\Operation;

use DateTimeInterface;
use DY\CFC\Operation\Exception\WrongOperationTypeException;
use DY\CFC\User\UserInterface;

class Operation implements OperationInterface
{
    private const DEPOSIT = "deposit";
    private const WITHDRAW = "withdraw";

    private bool $deposit;

    /**
     * @throws WrongOperationTypeException
     */
    public function __construct(
        private DateTimeInterface $date,
        string $type,
        private float $amount,
        private string $currency,
        private UserInterface $user
    ) {
        if ($type !== self::DEPOSIT && $type !== self::WITHDRAW) {
            throw new WrongOperationTypeException();
        }

        $this->deposit = $type === self::DEPOSIT;
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
        return $this->deposit;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getFee(): float
    {
        return 0;
    }
}
