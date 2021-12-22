<?php

declare(strict_types=1);

namespace DY\CFC;

use DY\CFC\Currency\CurrencyService;
use DY\CFC\Currency\CurrencyServiceInterface;
use DY\CFC\Exception\IncorrectInputException;
use DY\CFC\Operation\OperationInterface;
use DY\CFC\Operation\OperationService;
use DY\CFC\Operation\OperationServiceInterface;
use DY\CFC\User\UserService;
use DY\CFC\User\UserServiceInterface;

class CommissionFeeCalculatorApplication
{
    public function __construct(
        private UserServiceInterface $userService,
        private OperationServiceInterface $operationService,
        private CurrencyServiceInterface $currencyService
    ) {
    }

    public static function create(): CommissionFeeCalculatorApplication
    {
        return new CommissionFeeCalculatorApplication(
            UserService::create(),
            OperationService::create(),
            CurrencyService::create()
        );
    }

    /**
     * @throws IncorrectInputException
     */
    public function process(string $input): string
    {
        $data = str_getcsv($input);

        if (!is_array($data) || (count($data) !== 6)) {
            throw new IncorrectInputException();
        }

        [$date, $userID, $userType, $type, $amount, $currency] = $data;

        $user = $this->userService->findOrAddNew($userID, $userType);
        $precision = $this->currencyService->getPrecision($amount);
        $currency = $this->currencyService->findOrAddNew($currency, $precision);

        $operation = $this->operationService->addNew($date, $type, $amount, $currency, $user);
        return $this->currencyService->format($operation->getCurrency(), $operation->getFee());
    }

    /**
     * @throws IncorrectInputException
     */
    public function processMultiline(string $input): string
    {
        $result = [];
        $inputLines = explode(PHP_EOL, $input);

        foreach ($inputLines as $inputLine) {
            $result[] = $this->process($inputLine);
        }

        return join(PHP_EOL, $result);
    }
}
