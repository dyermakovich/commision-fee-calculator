<?php

declare(strict_types=1);

namespace DY\CFC\Operation;

use DateTimeInterface;
use DY\CFC\Currency\CurrencyInterface;
use DY\CFC\Operation\Exception\WrongOperationTypeException;
use DY\CFC\User\UserInterface;

abstract class OperationAbstract implements OperationInterface
{
    public const DEPOSIT = "deposit";
    public const WITHDRAW = "withdraw";

    private bool $deposit;
    private ?OperationInterface $previous;

    /**
     * @throws WrongOperationTypeException
     */
    public function __construct(
        private DateTimeInterface $date,
        string $type,
        private float $amount,
        private CurrencyInterface $currency,
        private UserInterface $user
    ) {
        if ($type !== self::DEPOSIT && $type !== self::WITHDRAW) {
            throw new WrongOperationTypeException();
        }

        $this->deposit = $type === self::DEPOSIT;
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
        if ($type === self::DEPOSIT) {
            return new Deposit($date, $amount, $currency, $user);
        }
        return new Withdraw($date, $amount, $currency, $user);
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

    public function getCurrency(): CurrencyInterface
    {
        return $this->currency;
    }

    public function getFee(): float
    {
        return $this->getAmountForCharge() * $this->getCommissionRate();
    }

    public function getPrevious(): ?OperationInterface
    {
        return $this->previous;
    }
}
