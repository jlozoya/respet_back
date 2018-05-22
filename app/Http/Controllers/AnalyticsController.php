<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;
use App\Models\Contact;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;
use App\Notifications\RegistrationConfirmation;
use Carbon\Carbon;

class AnalyticsController extends BaseController
{
    /**
     * Recupera la información básica del estado del servidor.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getAnalytics() {
        $userNumber = User::count();
        $userMaleNumber = User::where('gender', 1)->count();
        $userFemaleNumber = User::where('gender', 2)->count();
        $contactNumber = Contact::count();
        return response()->json([
            'users_number' => $userNumber,
            'users_male_number' => $userMaleNumber,
            'users_female_number' => $userFemaleNumber,
            'contacts_number' => $contactNumber
        ], 200);
    }
}
