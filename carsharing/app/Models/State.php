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

class State
{
    const FILE_NAME = 'state';

    public static function on()
    {
        Storage::disk('local')->put(static::FILE_NAME, true);
    }

    public static function off()
    {
        Storage::disk('local')->put(static::FILE_NAME, false);
    }

    public static function toogle()
    {
        Storage::disk('local')->put(static::FILE_NAME, !static::get());
    }

    /**
     * @return bool
     */
    public static function get()
    {
        return (bool)Storage::disk('local')->get(static::FILE_NAME);
    }
}
