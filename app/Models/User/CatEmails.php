<?php

namespace App\Models\User\User;

use Illuminate\Database\Eloquent\Model;

class CatEmails extends Model 
{
    protected $table = 'cat_emails';
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
