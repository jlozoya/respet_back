<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model 
{
    protected $table = 'media';
    /**
     * Los atributos que son asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        // 'img', 'video', 'link', 'other'
        'type',
        'url',
        'alt',
        'width',
        'height',
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
