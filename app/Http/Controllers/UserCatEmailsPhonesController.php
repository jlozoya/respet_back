<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;
use App\Models\CatEmails;
use App\Models\CatPhones;

use App\Traits\PassportToken;

use Illuminate\Http\Request;

class UserCatEmailsPhonesController extends BaseController
{
    /**
     * Almacena listas de correos.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function addEmails(Request $request) {
        $this->validate($request, ['emails.*' => 'required|email',]);
        $user_id = $request->user()['id'];
        $emails = $request->get('emails');
        foreach ($emails as $email) {
            CatPhones::create([
                'user_id' => $user_id,
                'email' => $email,
            ]);
        }
        return response()->json($emails, 202);
    }
    /**
     * Almacena listas de teléfonos.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function addPhones(Request $request) {
        $this->validate($request, ['phones.*' => 'required|numeric|min:10',]);
        $user_id = $request->user()['id'];
        $phones = $request->get('phones');
        foreach ($phones as $phone) {
            CatPhones::create([
                'user_id' => $user_id,
                'phone' => $phone,
            ]);
        }
        return response()->json($phones, 202);
    }
    /**
     * Elimina un registro.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteEmail(Request $request, $id) {
        $email = CatEmails::find($id);
        if ($email['user_id'] == $request->user()['id']) {
            $email->delete();
            return response()->json(null, 204);
        } else {
            return response()->json('SERVER.WRONG_USER', 406);
        }
    }
    /**
     * Elimina un registro.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function deletePhone(Request $request, $id) {
        $phone = CatPhones::find($id);
        if ($phone['user_id'] == $request->user()['id']) {
            $phone->delete();
            return response()->json(null, 204);
        } else {
            return response()->json('SERVER.WRONG_USER', 406);
        }
    }
}
