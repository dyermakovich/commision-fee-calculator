<?php

declare(strict_types=1);

namespace DY\CFC\User;

interface UserServiceInterface
{
    public function findByID(string $id): ?UserInterface;

    public function addNew(string $id, string $type): UserInterface;

    public function findOrAddNew(string $id, string $type): UserInterface;
}
