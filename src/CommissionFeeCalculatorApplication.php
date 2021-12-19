<?php

declare(strict_types=1);

namespace DY\CFC;

use DY\CFC\Exception\IncorrectInputException;
use DY\CFC\Operation\OperationService;
use DY\CFC\Operation\OperationServiceInterface;
use DY\CFC\User\UserService;
use DY\CFC\User\UserServiceInterface;

class CommissionFeeCalculatorApplication
{
    public function __construct(
        private UserServiceInterface $userService,
        private OperationServiceInterface $operationService
    ) {
    }

    public static function create(): CommissionFeeCalculatorApplication
    {
        return new CommissionFeeCalculatorApplication(UserService::create(), OperationService::create());
    }

    /**
     * @throws IncorrectInputException
     */
    public function run(string $input): string
    {
        $lines = explode(PHP_EOL, $input);

        foreach ($lines as $line) {
            $data = str_getcsv($line);

            if (!is_array($data) || (count($data) !== 6)) {
                throw new IncorrectInputException();
            }

            [$date, $userID, $userType, $type, $amount, $currency] = $data;

            $user = $this->userService->findByID($userID);

            if (!isset($user)) {
                $user = $this->userService->addNew($userID, $userType);
            }

            $this->operationService->addNew($date, $type, $amount, $currency, $user);
        }

        $output = [];

        foreach ($this->operationService->getAll() as $operation) {
            $output[] = $operation->getAmount();
        }

        return join(PHP_EOL, $output);
    }
}
