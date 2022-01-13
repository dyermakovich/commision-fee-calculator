<?php

declare(strict_types=1);

namespace DY\CFC\Config;

use DY\CFC\FileLocator\FileLocatorInterface;
use Symfony\Component\Yaml\Yaml;

class Config implements ConfigInterface
{
    private const BASE_CURRENCY = 'base_currency';

    private array $params;

    public function __construct(private FileLocatorInterface $locator)
    {
        $this->params = Yaml::parseFile($this->locator->getConfigFilename());
    }

    public function getBaseCurrency(): string
    {
        return $this->params[self::BASE_CURRENCY];
    }
}
