<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    /**
     * Los atributos que son asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'roundsman_id',
        'price',
        // 'on_create' | 'stored' | 'on_transit' | 'delivered' | 'other'
        'state',
        'take_out_date',
        'delivery_date',
        'location_id',
        'destination_id',
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
