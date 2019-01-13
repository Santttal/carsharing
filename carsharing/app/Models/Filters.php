<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 01.07.18
 * Time: 19:34
 */

namespace App\Models;

use App\Lib\Car;
use Illuminate\Support\Facades\Storage;

class Filters
{
    const FILE_NAME = 'filters';

    /**
     * @var array
     */
    public $companies;
    /**
     * @var float
     */
    public $minOil;
    /**
     * @var float
     */
    public $maxPrice;

    public function save()
    {
        Storage::disk('local')->put(static::FILE_NAME, json_encode($this->toArray()));
    }

    /**
     * @return Filters
     */
    public static function load(): Filters
    {
        $data = json_decode(Storage::disk('local')->get(static::FILE_NAME), true);
        $filters = new static();
        $filters->companies = $data['companies'] ?? [];
        $filters->minOil = $data['min_oil'] ?? 100;
        $filters->maxPrice = $data['max_price'] ?? 8;

        return $filters;
    }

    /**
     * @param Car $car
     * @return bool
     */
    public function fits(Car $car): bool
    {
        return
            $this->companies[$car->getCompany()] &&
            $this->minOil <= $car->getOil() &&
            $this->maxPrice >= $car->getPrice();
    }

    public function toArray()
    {
        return [
            'companies' => $this->companies,
            'min_oil' => $this->minOil,
            'max_price' => $this->maxPrice,
        ];
    }
}
