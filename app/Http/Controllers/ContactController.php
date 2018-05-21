<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;
use App\Models\Contact;

use App\Notifications\ContactMessage;
use App\Notifications\ContactConfirmation;

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
        ]);
        $contact = Contact::create([
            'name' => $request->get('name'),
            'phone' => $request->get('phone'),
            'email' => $request->get('email'),
            'message' => $request->get('message'),
        ]);
        $contact->notify(new ContactConfirmation());
        $users = User::where('is_admin', true)->get();
        foreach ($users as $user) {
            $user->notify(new ContactConfirmation($contact, $user));
        }
        return response()->json($contact, 201);
    }
}
