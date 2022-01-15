<?php

declare(strict_types=1);

namespace DY\CFC\FileLocator;

use Symfony\Component\Config\FileLocator as SymfonyFileLocator;
use Symfony\Component\Config\FileLocatorInterface as SymfonyFileLocatorInterface;

class FileLocator implements FileLocatorInterface, SymfonyFileLocatorInterface
{
    public const SERVICES = 'services.yaml';
    public const CONFIG = 'config.yaml';

    private SymfonyFileLocatorInterface $fileLocator;

    public function __construct()
    {
        $this->fileLocator = new SymfonyFileLocator(dirname(__DIR__, 2) . '/config');
    }

    public function locate(string $name, string $currentPath = null, bool $first = true): array|string
    {
        return $this->fileLocator->locate($name, $currentPath, $first);
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
