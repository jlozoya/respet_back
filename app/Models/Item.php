<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model 
{
    /**
     * Los atributos que son asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'invoice_id',
        'item_name',
        'item_price',
        'item_qty',
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
