<?php

namespace App\Http\Middleware;

use Log;
use Closure;
use App\Models\User;

class CompanyRoleLevelOneMiddleware
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
        if ($request->get('company_id')) {
            $user_role = User::select('user_roles.role_id')
                ->where('Authorization', $request->header('Authorization'))
                ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
                ->where('company_id', $request->get('company_id'))
                ->first();
            if (isset($user_role->role_id)) {
                if ($user_role->role_id <= 1) {
                    return $next($request);
                }
                return response()->json('SERVER.NOT_ENOUGH_RIGHTS', 406);
            }
            return response()->json('SERVER.UNAUTHORIZED', 406);
        }
        return response()->json('SERVER.MISSED_COMPANY_ID', 406);
    }
}