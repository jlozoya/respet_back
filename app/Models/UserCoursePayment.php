<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCoursePayment extends Model 
{
    /**
     * Los atributos que son asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'course_price_id',
        'amount',
        'description',
        'charge_id',
        'status',
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
