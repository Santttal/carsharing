<?php

namespace App\Http\Controllers;

use App\Lib\Car;
use App\Lib\Delimobil;
use App\Lib\Telegram;
use App\Lib\Youdrive;
use App\Models\CarsharingPolygon;
use App\Models\CarStorage;
use App\Models\Filters;
use App\Models\Radius;
use App\Models\State;
use App\Models\SyncLog;
use Geokit\LatLng;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarsharingController extends Controller
{
    public function index()
    {
        return view(
            'index',
            [
                'polygons' => CarsharingPolygon::all('id', 'name', 'state', 'coordinates'),
                'radius' => Radius::load(),
                'home' => Radius::homeCoordinates(),
                'cars' => CarStorage::load(),
                'state' => State::get(),
                'filters' => Filters::load()
            ]
        );
    }

    public function editRadius()
    {
        return view(
            'radius',
            [
                'radius' => Radius::load(),
            ]
        );
    }

    public function updateRadius(Request $request)
    {
        $radius = Radius::load();
        if ($request->get('coordinates')) {
            $coordinates = explode(', ', $request->get('coordinates'));
            $radius->coordinates = new LatLng($coordinates[0], $coordinates[1]);
        }
        if ($request->get('amount')) {
            $radius->amount = $request->get('amount');
        }

        if ($request->get('state') !== null) {
            $radius->state = $request->get('state');
        }
        $radius->save();

        return redirect()->action('CarsharingController@index');
    }

    public function state(Request $request)
    {
        if ($request->get('state')) {
            State::on();
        } else {
            State::off();
        }

        return redirect()->action('CarsharingController@index');
    }

    public function updatePolygon($id, Request $request)
    {
        /** @var CarsharingPolygon $polygon */
        $polygon = CarsharingPolygon::findOrFail($id);
        $polygon->state = (bool)$request->get('state');
        $polygon->save();

        return redirect()->action('CarsharingController@index');
    }

    public function logs()
    {
        return view(
            'logs',
            [
                'logs' => SyncLog::load(),
            ]
        );
    }

    public function cars()
    {
        return new JsonResponse(array_map(function(Car $car) {
            $data = $car->toArray();
            $data['yandexStyle'] = $car->getYandexStyle();

            return $data;
        }, CarStorage::load()));
    }

    public function filters(Request $request)
    {
        var_dump($_REQUEST);

        $filters = Filters::load();
        $filters->minOil = $request->get('min_oil');
        $filters->maxPrice = $request->get('max_price');
        foreach ($request->get('companies') as $name => $enabled) {
            $filters->companies[$name] = (bool)$enabled;
        }

        $filters->save();

        return redirect()->action('CarsharingController@index');
    }
}
