<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailConfirm extends Model 
{
    protected $table = 'email_confirm';
    public $timestamps = false;
    /**
     * Los atributos que son asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'email',
        'token',
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
