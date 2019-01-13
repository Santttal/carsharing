<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 01.07.18
 * Time: 19:34
 */

namespace App\Models;

use Geokit\LatLng;
use Illuminate\Support\Facades\Storage;

class Radius
{
    const FILE_NAME = 'radius';

    /**
     * @var bool
     */
    public $state;
    /**
     * @var int
     */
    public $amount;
    /**
     * @var LatLng
     */
    public $coordinates;

    public function save()
    {
        $data = [
            'amount' => $this->amount,
            'state' => $this->state,
            'coordinates' => [
                'lat' => $this->coordinates->getLatitude(),
                'lng' => $this->coordinates->getLongitude(),
            ],
        ];

        Storage::disk('local')->put(static::FILE_NAME, json_encode($data));
    }

    /**
     * @return Radius
     */
    public static function load(): Radius
    {
        $radius = new Radius();
        $data = json_decode(Storage::disk('local')->get(static::FILE_NAME), true);
        $radius->amount = $data['amount'];
        $radius->state = $data['state'];
        $radius->coordinates = new LatLng($data['coordinates']['lat'], $data['coordinates']['lng']);

        return $radius;
    }

    public static function homeCoordinates(): LatLng
    {
        return new LatLng(60.051172, 30.431071);
    }
}
