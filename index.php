<?php

require_once __DIR__ . '/vendor/autoload.php';
Dotenv\Dotenv::create('.')->load();

function dd($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    die();
}

use App\Service\Easynvest;

$teste = new Easynvest();
try {
    $teste->getStockInformation('IT UB4');
} catch (Exception $e) {
    echo
        'Status Code: ' . $e->getCode() . PHP_EOL .
        'Error: ' . $e->getMessage() . PHP_EOL .
        'In line: ' . $e->getLine();
}