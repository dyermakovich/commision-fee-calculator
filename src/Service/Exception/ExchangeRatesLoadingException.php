<?php

declare(strict_types=1);

namespace DY\CFC\Service\Exception;

use Exception;
use Throwable;

class ExchangeRatesLoadingException extends Exception
{
    public function __construct(string $message = "", Throwable $previous = null)
    {
        parent::__construct($message, previous: $previous);
    }
}
