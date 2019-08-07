<?php

namespace App\Http\Controllers\User;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User\User;
use App\Models\User\UserPermissions;

use Illuminate\Http\Request;

class UserPermissionsController extends BaseController
{
    /**
     * Recupera los permisos establecidos por el usuario.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getUserPermissions(Request $request) {
        return response()->json(UserPermissions::find($request->user()['permissions_id']), 200);
    }
    /**
     * Establece los permisos que el usuario otorga.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function setUserPermissions(Request $request) {
        $userPermissions = UserPermissions::find($request->user()['permissions_id']);
        foreach(array_keys($userPermissions->toArray()) as $key) {
            if ($request->get($key) !== null) {
                $userPermissions[$key] = $request->get($key);
            }
        }
        $userPermissions->save();
        return response()->json($userPermissions, 202);
    }
}
