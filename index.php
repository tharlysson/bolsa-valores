<?php

require_once __DIR__ . '/vendor/autoload.php';
Dotenv\Dotenv::create('.')->load();

use App\Service\Easynvest;

$teste = new Easynvest();
try {
    print_r($teste->getStockInformation('ITUB4'));
} catch (Exception $e) {
    echo
        'Status Code: ' . $e->getCode() . PHP_EOL .
        'Error: ' . $e->getMessage() . PHP_EOL .
        'In line: ' . $e->getLine();
}