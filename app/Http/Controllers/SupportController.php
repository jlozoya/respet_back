<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;
use App\Models\Support;

use Illuminate\Http\Request;

use App\Notifications\SupportConfirmation;
use App\Notifications\SupportMessage;

class SupportController extends BaseController
{
    /**
     * Llama la función para solicitar soporte.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $this->validate($request, [
            'name' => 'required|min:4|max:60',
            'email' => 'required|max:60',
            'message' => 'required|max:255',
            'lang' => 'required',
        ]);
        $support = Support::create([
            'name' => $request->get('name'),
            'phone' => $request->get('phone'),
            'email' => $request->get('email'),
            'message' => $request->get('message'),
            'lang' => $request->get('lang'),
        ]);
        $support->notify(new SupportConfirmation($support['lang']));
        $users = User::where('role', 'admin')->get();
        if ($users) {
            foreach ($users as $user) {
                $user->notify(new SupportMessage($support, $user));
            }
        }
        return response()->json($support, 201);
    }
}
