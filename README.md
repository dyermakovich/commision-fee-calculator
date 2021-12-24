# Commission fee calculator

Get your EXCHANGE_RATES_API_KEY from exchangeratesapi.io.

In root directory there is input.csv file with some data, 
you can modify it if you need to change input data.

To run application you can use your local PHP >= 8.0 and Composer:
```
php script.php input.csv EXCHANGE_RATES_API_KEY
```
To run tests you don't need API key.
```
php vendor\bin\phpunit tests 
```
or
```
composer test 
```
To run application you can use docker as well. 
In this case you don't need PHP and Composer installed locally:
```
docker-compose build
docker-compose up -d
docker-compose exec www php script.php input.csv EXCHANGE_RATES_API_KEY
docker-compose exec www composer test
docker-compose exec www php vendor/bin/phpunit tests
docker-compose down
```
