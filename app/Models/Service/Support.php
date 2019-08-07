<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;

class Support extends Model 
{
    use Notifiable;

    protected $table = 'support';
    /**
     * Los atributos que son asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'phone',
        'email',
        'message',
        'lang',
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
