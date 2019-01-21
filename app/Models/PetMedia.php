<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetMedia extends Model
{
    /**
     * Los atributos que son asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'pet_id',
        'media_id',
        'updated_at',
        'created_at',
    ];
    /**
     * Los atributos excluidos del formulario JSON del modelo.
     *
     * @var array
     */
    protected $hidden = [
    ];
}