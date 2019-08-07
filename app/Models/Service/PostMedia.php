<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;

class PostMedia extends Model
{
    /**
     * Los atributos que son asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'post_id',
        'media_id',
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
