<?php

declare(strict_types=1);

namespace DY\CFC\FileLocator;

class FileLocator extends \Symfony\Component\Config\FileLocator implements FileLocatorInterface
{
    public const SERVICES = 'services.yaml';
    public const CONFIG = 'config.yaml';

    public function __construct()
    {
        parent::__construct(dirname(__DIR__, 2) . '/config');
    }

    public function getServicesFilename(): string
    {
        return $this->locate(self::SERVICES);
    }

    public function getConfigFilename(): string
    {
        return $this->locate(self::CONFIG);
    }
}
