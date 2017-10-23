<?php

namespace App\Http\Models;

use Jenssegers\Mongodb\Model as Moloquent;

class Airports extends Moloquent
{

    public function scopeIndonesia($query)
    {
        return $query->where('country', 'Indonesia');
    }

    public function scopeQuery($query, $keyword)
    {
        return $query->whereRaw(
            [
                '$or' => [
                    ['city' => new \MongoRegex("/$keyword/i")],
                    ['name' => new \MongoRegex("/$keyword/i")],
                    ['iata' => new \MongoRegex("/$keyword/i")],
                ]
            ]
        );
    }

}