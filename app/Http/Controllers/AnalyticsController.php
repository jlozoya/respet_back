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
        // Genero
        $userMaleNumber = User::where('gender', 1)->count();
        $userFemaleNumber = User::where('gender', 2)->count();
        $contactNumber = Contact::count();
        // Edad
        // 0 a 11 años
        $children = User::whereBetween('birthday', [Carbon::now()->subYears(11), Carbon::now()])->count();
        // 11 a 18 años
        $teens = User::whereBetween('birthday', [Carbon::now()->subYears(18), Carbon::now()->subYears(11)->subDay(1)])->count();
        // 18 a 25 años
        $young_adults = User::whereBetween('birthday', [Carbon::now()->subYears(25), Carbon::now()->subYears(18)->subDay(1)])->count();
        // Edad desconocida
        $unknown_age = User::whereNull('birthday')->count();
        return response()->json([
            'users_number' => $userNumber,
            'gender' => [
                'male_number' => $userMaleNumber,
                'female_number' => $userFemaleNumber,
            ],
            'contacts_number' => $contactNumber,
            'ages' => [
                'children' => $children,
                'teens' => $teens,
                'young_adults' => $young_adults,
                'unknown' => $unknown_age,
            ]
        ], 200);
    }
}
