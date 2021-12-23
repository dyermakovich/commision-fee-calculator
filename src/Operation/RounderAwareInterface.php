<?php

declare(strict_types=1);

namespace DY\CFC\Operation;

use DY\CFC\Service\RounderInterface;

interface RounderAwareInterface
{
    public function setRounder(RounderInterface $rounder);
}
