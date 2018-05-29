<?php

namespace App\Http\Middleware;

use Log;
use Closure;
use App\Models\User;

class IsAdmin
{
    /**
     * Este middleware valida que los usuarios tengan permisos nivel uno para para ejecutar
     * las funciones de las rutas en este nivel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if ($user) {
            if ($user['role'] == 'admin') {
                return $next($request);
            }
            return response()->json('SERVER.NOT_ENOUGH_RIGHTS', 406);
        }
        return response()->json('SERVER.USER_NOT_REGISTRED', 404);
    }
}