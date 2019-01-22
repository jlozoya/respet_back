<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
// OAuth
use Laravel\Passport\HasApiTokens;
// Reiniciar constraseña
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable;

class User extends Model implements CanResetPasswordContract, AuthenticatableContract, AuthorizableContract
{
    use HasApiTokens, Notifiable, Authenticatable, Authorizable, CanResetPassword;
    
    /**
     * Los atributos que son asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'first_name',
        'last_name',
        'gender',
        'email',
        'password',
        'media_id',
        'phone',
        'lang',
        'birthday',
        'confirmed',
        // 'password', 'google', 'facebook', 'instagram', 'twitter', 'other'
        'grant_type',
        'role',
        'direction_id',
        'created_at',
    ];
    /**
     * Los atributos excluidos del formulario JSON del modelo.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'confirmed',
        'password',
    ];
}
