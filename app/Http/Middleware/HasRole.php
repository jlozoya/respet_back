<?php

namespace App\Http\Middleware;

use Closure;

class HasRole
{
    /**
     * Este middleware valida que el usuario disponga de determinados roles
     * para perimir su acceso.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  String ... $roles
     * @return mixed
     */
    public function handle($request, Closure $next, ... $roles)
    {
        $user = $request->user();
        if ($user) {
            foreach ($roles as $role) {
                if ($user['role'] == ($role))
                    return $next($request);
            }
            return response()->json('SERVER.NOT_ENOUGH_RIGHTS', 406);
        }
        return response()->json('SERVER.USER_NOT_REGISTRED', 404);
    }
}