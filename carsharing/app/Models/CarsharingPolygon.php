<?php

namespace App\Models;

use Geokit\LatLng;
use Geokit\Polygon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Polygon coordinates
 */
class CarsharingPolygon extends Model
{
    use SoftDeletes;

    protected $table = 'polygons';

    public function getCoordinatesAttribute($value)
    {
        $body = json_decode($value, true);
        $coors = [];
        foreach ($body as $item) {
            $coors[] = new LatLng($item['lat'], $item['lng']);
        }

        return new Polygon($coors);
    }

    public function getJsArrayAttribute()
    {
        $body = json_decode($this->attributes['coordinates'], true);
        $coors = [];
        foreach ($body as $item) {
            $coors[] = '[' . $item['lat'] . ', ' . $item['lng'] . ']';
        }

        return implode(', ' , $coors);
    }

    public function setCoordinatesAttribute(Polygon $polygon)
    {
        $coors = [];
        /** @var LatLng $item */
        foreach ($polygon as $item) {
            $coors[] = ['lat' => $item->getLatitude(), 'lng' => $item->getLongitude()];
        }
        $this->attributes['coordinates'] = json_encode($coors);
    }
}
