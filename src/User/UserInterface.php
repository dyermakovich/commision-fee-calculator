<?php

declare(strict_types=1);

namespace DY\CFC\User;

interface UserInterface
{
    public function getID(): int;

    public function isBusiness(): bool;
}
