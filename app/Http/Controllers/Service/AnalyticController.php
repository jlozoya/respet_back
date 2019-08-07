<?php

namespace App\Http\Controllers\Service;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User\User;
use App\Models\Service\Support;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class AnalyticController extends BaseController
{
    /**
     * Recupera la información básica del estado del servidor.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBasicAnalytics() {
        $userNumber = User::count();
        // Genero
        $userMaleNumber = User::where('gender', 1)->count();
        $userFemaleNumber = User::where('gender', 2)->count();
        $supportNumber = Support::count();

        $userSouceApp = User::where('grant_type', 1)->count();
        $userSouceFacebook = User::where('grant_type', 2)->count();
        $userSouceGoogle = User::where('grant_type', 3)->count();
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
            'supports_number' => $supportNumber,
            'ages' => [
                'children' => $children,
                'teens' => $teens,
                'young_adults' => $young_adults,
                'unknown' => $unknown_age,
            ],
            'grant_types' => [
                'password' => $userSouceApp,
                'facebook' => $userSouceFacebook,
                'google' => $userSouceGoogle
            ]
        ], 200);
    }
    /**
     * Recupera la información básica del estado del servidor.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getUsersRegistration(Request $request) {
        $this->validate($request, ['interval' => 'required',]);
        switch ($request->get('interval')) {
            case 'lastWeek': {
                $users = DB::select(
                    'SELECT usr.created_at, count(*) AS users FROM (SELECT date(created_at) AS created_at FROM users WHERE created_at BETWEEN "'
                    . Carbon::now()->subWeek() . '" AND "' . Carbon::now() . '" GROUP BY created_at) AS usr GROUP BY usr.created_at'
                );
                return response()->json($users, 200);
            }
            break;
            case 'lastMonth': {
                $users = DB::select(
                    'SELECT usr.created_at, count(*) AS users FROM (SELECT date(created_at) AS created_at FROM users WHERE created_at BETWEEN "'
                    . Carbon::now()->subMonth() . '" AND "' . Carbon::now() . '" GROUP BY created_at) AS usr GROUP BY usr.created_at'
                );
                return response()->json($users, 200);
            }
            break;
            case 'lastYear': {
                $users = DB::select(
                    'SELECT usr.created_at, count(*) AS users FROM (SELECT date(created_at) AS created_at FROM users WHERE created_at BETWEEN "'
                    . Carbon::now()->subYear() . '" AND "' . Carbon::now() . '" GROUP BY created_at) AS usr GROUP BY usr.created_at'
                );
                return response()->json($users, 200);
            }
            break;
        };
        return response()->json([], 200);
    }
}
