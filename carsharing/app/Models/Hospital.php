<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
        'lat',
        'lng'
    ];
}
