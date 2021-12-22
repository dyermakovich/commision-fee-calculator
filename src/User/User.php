<?php

declare(strict_types=1);

namespace DY\CFC\User;

use DY\CFC\Operation\OperationInterface;
use DY\CFC\User\Exception\WrongUserIDException;
use DY\CFC\User\Exception\WrongUserTypeException;

class User implements UserInterface
{
    public const PRIVATE = "private";
    public const BUSINESS = "business";

    private int $id;
    private bool $business;

    private array $operations = [];

    /**
     * @throws WrongUserIDException
     * @throws WrongUserTypeException
     */
    public function __construct(int $id, string $type = self::PRIVATE)
    {
        if ($id === 0) {
            throw new WrongUserIDException();
        }

        $this->id = $id;

        if ($type !== self::BUSINESS && $type !== self::PRIVATE) {
            throw new WrongUserTypeException();
        }

        $this->business = $type === self::BUSINESS;
    }

    public function getID(): int
    {
        return $this->id;
    }

    public function isBusiness(): bool
    {
        return $this->business;
    }

    public function addOperation(OperationInterface $operation): void
    {
        $this->operations[] = $operation;
    }

    public function getLastOperation(): ?OperationInterface
    {
        return empty($this->operations) ? null : $this->operations[count($this->operations) - 1];
    }
}
