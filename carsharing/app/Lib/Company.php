<?php

namespace App\Lib;

abstract class Company
{
    const NAME = '';

    /**
     * @return Car[]
     */
    abstract public function loadCars(): array;

    public function getName()
    {
        return static::NAME;
    }

    public function makeOrder(Car $car)
    {

    }
}
