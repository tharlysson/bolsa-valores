<?php

namespace App\Entity;

class Stock
{
    private $code;
    private $name;
    private $price;
    private $open;
    private $maxDay;
    private $minDay;
    private $lastUpdate;

    public function __get($value)
    {
        return $this->$value;
    }

    public function __set($prop, $value)
    {
        $this->$prop = $value;
    }
}