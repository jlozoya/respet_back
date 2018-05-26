<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;
use App\Models\Contact;

use Illuminate\Http\Request;

use App\Notifications\ContactConfirmation;
use App\Notifications\ContactMessage;

class ContactController extends BaseController
{
    /**
     * Llama la función para enviar un correo de confirmación.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function sendContact(Request $request) {
        $this->validate($request, [
            'name' => 'required|min:4|max:60',
            'email' => 'required|max:60',
            'message' => 'required|max:255',
            'lang' => 'required',
        ]);
        $contact = Contact::create([
            'name' => $request->get('name'),
            'phone' => $request->get('phone'),
            'email' => $request->get('email'),
            'message' => $request->get('message'),
            'lang' => $request->get('lang'),
        ]);
        $contact->notify(new ContactConfirmation($contact['lang']));
        $users = User::where('role', 'admin')->get();
        if ($users) {
            foreach ($users as $user) {
                $user->notify(new ContactMessage($contact, $user));
            }
        }
        return response()->json($contact, 201);
    }
}
