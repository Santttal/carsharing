<?php

namespace App\Lib;

use Geokit\LatLng;

class Delimobil extends Company
{
    const NAME = 'Delimobil';

    const DEFAULT_SPB_PRICE = 8;

    /**
     * @return Car[]
     */
    public function loadCars(): array
    {
        $client = new \GuzzleHttp\Client();
        $carsData = json_decode($client->get('https://delimobil.ru/maps-data?action=cars&alias=spb')->getBody(), true)['features'];
        $cars = [];

        foreach ($carsData as $car) {
            $carCoordArray = $car["geometry"]["coordinates"];
            $carCoord = new LatLng($carCoordArray[1], $carCoordArray[0]);
            $cars[] = new Car('no_id', $this->getName(), $carCoord, round($car['fuel']), self::DEFAULT_SPB_PRICE);
        }

        return $cars;
    }
}
