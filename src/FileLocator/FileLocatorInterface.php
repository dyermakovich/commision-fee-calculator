<?php

declare(strict_types=1);

namespace DY\CFC\FileLocator;

interface FileLocatorInterface
{
    public function getServicesFilename(): string;

    public function getConfigFilename(): string;
}
