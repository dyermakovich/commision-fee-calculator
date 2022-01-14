<?php

declare(strict_types=1);

namespace DY\CFC;

use DY\CFC\Currency\CurrencyServiceInterface;
use DY\CFC\Exception\IncorrectInputException;
use DY\CFC\Operation\OperationServiceInterface;
use DY\CFC\Service\ExchangeRateLoader;
use DY\CFC\User\UserServiceInterface;
use Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use DY\CFC\FileLocator\FileLocator;

class CommissionFeeCalculatorApplication
{
    public function __construct(
        private UserServiceInterface $userService,
        private OperationServiceInterface $operationService,
        private CurrencyServiceInterface $currencyService
    ) {
    }

    /**
     * @throws Exception
     */
    public static function create(bool $forTests = false): CommissionFeeCalculatorApplication
    {
        $container = new ContainerBuilder();

        $loader = new YamlFileLoader($container, new FileLocator());
        $loader->load(FileLocator::SERVICES);

        if ($forTests) {
            $container->getDefinition(ExchangeRateLoader::class)
                ->setFactory([ExchangeRateLoader::class, 'createMock']);
        }

        $container->compile();

        return $container->get(CommissionFeeCalculatorApplication::class);
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
