<?php

namespace App\Models\PayPal;

use Illuminate\Database\Eloquent\Model;

class IPNStatus extends Model 
{
    /**
     * Los atributos que son asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'payload',
        'status',
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
