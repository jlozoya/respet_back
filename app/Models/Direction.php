<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direction extends Model 
{
    /**
     * Los atributos que son asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'country',
        'administrative_area_level_1',
        'administrative_area_level_2',
        'route',
        'street_number',
        'postal_code',
        'lat',
        'lng',
    ];
    /**
     * Los atributos excluidos del formulario JSON del modelo.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
