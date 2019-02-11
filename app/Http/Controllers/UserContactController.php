<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;
use App\Models\EmailConfirm;
use App\Models\Direction;
use App\Models\Media;
use App\Models\UserPermissions;
use App\Models\CatEmail;
use App\Models\CatPhone;

use Illuminate\Http\Request;

class UserContactController extends BaseController
{
    /**
     * Recupera el la información de un usuario.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getUserContact(Request $request) {
        $user = $request->user();
        return response()->json([
            'id' => $user['id'],
            'name' => $user['name'],
            'contact' => [
                'email' => $user['email'],
                'emails' => CatEmail::where('user_id', $user['id'])->get(),
                'phone' => $user['phone'],
                'phones' => CatPhone::where('user_id', $user['id'])->get(),
            ]
        ], 200);
    }
    /**
     * Recupera la información de contacto de un usuario.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getUserContactById(Request $request, $id) {
        $user = User::find($id);
        $permissions = UserPermissions::find($user['permissions_id']);
        $contact;
        if ($permissions['show_main_email']) {
            $contact['email'] = $user['email'];
        }
        if ($permissions['show_alternative_emails']) {
            $contact['emails'] = CatEmail::where('user_id', $id)->get();
        }
        if ($permissions['show_main_phone']) {
            $contact['phone'] = $user['phone'];
        }
        if ($permissions['show_alternative_phones']) {
            $contact['phones'] = CatPhone::where('user_id', $id)->get();
        }
        if ($permissions['show_direction']) {
            if ($user['direction_id']) {
                $contact['direction'] = Direction::find($user['direction_id']);
            }
        }
        return response()->json([
            'id' => $user['id'],
            'name' => $user['name'],
            'contact' => $contact
        ], 200);
    }
}
