<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HealthStat extends Model
{
    /**
     * Los atributos que son asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'pet_id',
        'name',
        'description',
    ];
    /**
     * Los atributos excluidos del formulario JSON del modelo.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];
}
