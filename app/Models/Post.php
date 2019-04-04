<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * Los atributos que son asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'description',
        // 'found' | 'lost' | 'on_adoption' | 'on_sale' | 'on_hold' | 'other'
        'state',
        'direction_id',
        'direction_accuracy',
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
