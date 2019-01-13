<?php

namespace App\Models;

use App\Lib\Car;
use DeepCopy\Filter\Filter;
use Geokit\LatLng;
use Illuminate\Support\Facades\Storage;

class CarStorage
{
    const FILE_NAME = 'cars';

    /**
     * @param Car[] $newCars
     */
    public static function add(array $newCars)
    {
        $cars = static::load();
        $cars = array_merge($cars, $newCars);
        static::save($cars);

    }

    /**
     * @param Car[] $cars
     */
    public static function save(array $cars)
    {
        $data = [];
        foreach ($cars as $item) {
            $data[] = $item->toArray();
        }

        Storage::disk('local')->put(static::FILE_NAME, json_encode($data));
    }

    /**
     * @return Car[]
     */
    public static function load(): array
    {
        $filters = Filters::load();
        $cars = [];
        $data = json_decode(Storage::disk('local')->get(static::FILE_NAME), true);
        foreach ($data as $item) {
            $car = new Car($item['id'], $item['company'], new LatLng($item['coordinates'][0], $item['coordinates'][1]), $item['oil'], $item['price']);
            if ($filters->fits($car)) {
                $cars[] = $car;
            }
        }

        return $cars;
    }

    public static function purge()
    {
        static::save([]);
    }
}
