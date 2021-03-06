<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model 
{
    /**
     * Los atributos que son asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'extern_id',
        // 'google', 'facebook', 'instagram', 'twitter', 'other'
        'grant_type',
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
