<?php

namespace App\Http\Controllers\User;

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
        $this->validate($request, ['emails.*' => 'required|email|max:60',]);
        $user_id = $request->user()['id'];
        $emails = $request->get('emails');
        $savedEmails = [];
        foreach ($emails as $email) {
            array_push($savedEmails, CatEmails::create([
                'user_id' => $user_id,
                'email' => $email,
            ]));
        }
        return response()->json($savedEmails, 202);
    }
    /**
     * Almacena listas de teléfonos.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function addPhones(Request $request) {
        $this->validate($request, ['phones.*' => 'required|numeric|min:10|max:20',]);
        $user_id = $request->user()['id'];
        $phones = $request->get('phones');
        $savedPhones = [];
        foreach ($phones as $phone) {
            array_push($savedPhones, CatPhones::create([
                'user_id' => $user_id,
                'phone' => $phone,
            ]));
        }
        return response()->json($savedPhones, 202);
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
