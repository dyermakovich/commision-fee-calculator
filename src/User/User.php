<?php

declare(strict_types=1);

namespace DY\CFC\User;

use DY\CFC\Service\IntegerParser;
use DY\CFC\User\Exception\WrongUserIDException;
use DY\CFC\User\Exception\WrongUserTypeException;

class User implements UserInterface
{
    private const PRIVATE = "private";
    private const BUSINESS = "business";

    private int $id;
    private bool $business;

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
}
