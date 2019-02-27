<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;
use App\Models\EmailConfirm;
use App\Models\Address;
use App\Models\Media;
use App\Models\UserPermissions;
use App\Models\CatEmails;
use App\Models\CatPhones;

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
                'emails' => CatEmails::where('user_id', $user['id'])->get(),
                'phone' => $user['phone'],
                'phones' => CatPhones::where('user_id', $user['id'])->get(),
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
        $contact = null;
        if ($permissions['show_main_email']) {
            $contact['email'] = $user['email'];
        }
        if ($permissions['show_alternative_emails']) {
            $contact['emails'] = CatEmails::where('user_id', $id)->get();
        }
        if ($permissions['show_main_phone']) {
            $contact['phone'] = $user['phone'];
        }
        if ($permissions['show_alternative_phones']) {
            $contact['phones'] = CatPhones::where('user_id', $id)->get();
        }
        if ($permissions['show_address']) {
            if ($user['address_id']) {
                $contact['address'] = Address::find($user['address_id']);
            }
        }
        return response()->json([
            'id' => $user['id'],
            'name' => $user['name'],
            'contact' => $contact
        ], 200);
    }
}
