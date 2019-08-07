<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;

class Bulletin extends Model 
{
    /**
     * Los atributos que son asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'title',
        'description',
        'date',
        'media_id',
        'created_at',
        'updated_at',
    ];
    /**
     * Los atributos excluidos del formulario JSON del modelo.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
