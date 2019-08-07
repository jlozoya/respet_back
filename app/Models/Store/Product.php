<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * Los atributos que son asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'amount',
        'price',
        'warehouse_id',
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
