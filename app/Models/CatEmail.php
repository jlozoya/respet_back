<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatEmail extends Model 
{
    /**
     * Los atributos que son asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'email',
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
