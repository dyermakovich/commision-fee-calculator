<?php

declare(strict_types=1);

namespace DY\CFC\Config;

use DY\CFC\FileLocator\FileLocator;
use DY\CFC\FileLocator\FileLocatorInterface;
use Symfony\Component\Yaml\Yaml;

class Config implements ConfigInterface
{
    private const DEPOSIT = 'deposit';
    private const WITHDRAW = 'withdraw';
    private const COMMISSION = 'commission';
    private const PRIVATE = 'private';
    private const BUSINESS = 'business';
    private const FREE_OF_CHARGE = 'free_of_charge';
    private const AMOUNT = 'amount';
    private const BASE_CURRENCY = 'base_currency';
    private const COUNT = 'count';

    private array $params;

    public function __construct(private FileLocatorInterface $locator)
    {
        $this->params = Yaml::parseFile($this->locator->getConfigFilename());
    }

    public static function create(): ConfigInterface
    {
        return new Config(new FileLocator());
    }

    public function getDepositCommission(): float
    {
        return $this->params[self::DEPOSIT][self::COMMISSION];
    }

    public function getWithdrawPrivateCommission(): float
    {
        return $this->params[self::WITHDRAW][self::PRIVATE][self::COMMISSION];
    }

    public function getWithdrawPrivateFreeOfChargeAmount(): float
    {
        return $this->params[self::WITHDRAW][self::PRIVATE][self::FREE_OF_CHARGE][self::AMOUNT];
    }

    public function getBaseCurrency(): string
    {
        return $this->params[self::BASE_CURRENCY];
    }

    public function getWithdrawPrivateFreeOfChargeCount(): int
    {
        return $this->params[self::WITHDRAW][self::PRIVATE][self::FREE_OF_CHARGE][self::COUNT];
    }

    public function getWithdrawBusinessCommission(): float
    {
        return $this->params[self::WITHDRAW][self::BUSINESS][self::COMMISSION];
    }
}
