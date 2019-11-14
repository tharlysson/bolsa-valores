<?php

require_once __DIR__ . '/vendor/autoload.php';
Dotenv\Dotenv::create('.')->load();

function dd($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    die();
}

