<?php

namespace DY\CFC\Service;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;

class Parser implements ParserInterface
{
    public static function create(): ParserInterface
    {
        return new Parser();
    }

    public function parseFloat(string $value, ?float $defaultValue = null): ?float
    {
        if (!is_numeric($value)) {
            return $defaultValue;
        }

        return floatval($value);
    }

    public function parseInteger(string $value, ?int $defaultValue = null): ?int
    {
        if (!is_numeric($value)) {
            return $defaultValue;
        }

        return intval($value);
    }

    public function parseDate(string $date, ?DateTimeInterface $defaultValue = null): ?DateTimeInterface
    {
        try {
            return new DateTimeImmutable($date);
        } catch (Exception) {
            return $defaultValue;
        }
    }
}
