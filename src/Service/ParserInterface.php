<?php

namespace DY\CFC\Service;

use DateTimeInterface;

interface ParserInterface
{
    public function parseFloat(string $value, ?float $defaultValue = null): ?float;

    public function parseInteger(string $value, ?int $defaultValue = null): ?int;

    public function parseDate(string $date, ?DateTimeInterface $defaultValue = null): ?DateTimeInterface;
}
