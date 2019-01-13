<?php

namespace App\Lib;

use Geokit\LatLng;

class Youdrive extends Company
{
    const NAME = 'YouDrive';

    /**
     * @return Car[]
     */
    public function loadCars(): array
    {
        $client = new \GuzzleHttp\Client();

        $headers = [
            'User-Agent' => env('YOUDRIVE_USER_AGENT'),
            'Connection' => 'Keep-Alive',
            'Accept-Encoding' => 'gzip',
            'cookie' => 'session_id=' . env('SESSION_ID')
        ];

        $carsData = json_decode($client->get(
            'https://youdrive.today/status?lat=60.0509673&lon=30.4311021&version=6',
            [
                'headers' => $headers,
            ]
        )->getBody(), true)['cars'];

        $cars = [];
        foreach ($carsData as $car) {
            if ($car["not_available"]) {
                continue;
            }
            $cars[] = new Car($car["car_id"], $this->getName(), new LatLng($car["lat"], $car["lon"]), round($car['fuel']), round($car['tariff']['usage']['price']/100));
        }

        return $cars;
    }

    public function makeOrder(Car $car)
    {
        // not implemented yet
    }
}
