<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;
use Laravel\Passport\Client;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class OAuthController extends BaseController
{
    /**
     * Valida las credenciales del usuario en caso de ser correctas devuelve la demás información del usuario.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function issueToken(Request $request) {
        $this->validate($request, [
            'grant_type' => 'required',
        ]);
        $error = $this->checkClient($request);
        if ($error) {
            return $error;
        }
        switch ($request->get('grant_type')) {
            case 'password': {
                return $this->passwordLogin($request);
            } break;
            case 'google': {
                return $this->googleLogin($request);
            } break;
            case 'facebook': {
                return $this->facebookLogin($request);
            } break;
            default: {
                return response()->json('SERVER.INCORRECT_CREDENTIALS', 406);
            }
        }
    }
    
    private function passwordLogin(Request $request) {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6|max:60',
        ]);
        $user = User::where([
            'email' => $request->get('email'), 'source' => $request->get('source')
        ])->first();
        if ($user && Hash::check($request->get('password'), $user['password'])) {
            $sesion['id'] = $user['id'];
            $sesion['token'] = $user
            ->createToken(env('APP_OAUTH_PASS', 'OAuth'))->accessToken;
            return response()->json($sesion, 200);
        } else {
            return response()->json('SERVER.INCORRECT_USER', 406);
        }
    }
    /**
     * Verifica las credenciales del cliente con las ge se hace el login.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    private function checkClient(Request $request) {
        $this->validate($request, [
            'client_id' => 'required',
            'client_secret' => 'required',
        ]);
        if (Client::where(['id' => $request->get('client_id'), 'secret' => $request->get('client_secret')])->first()) {
            return;
        } else {
            return response()->json([
                "error" => "invalid_client",
                "message" => "Client authentication failed"
            ], 401);
        }
    }

    private function googleLogin(Request $request) {
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->get('https://www.googleapis.com/oauth2/v1/tokeninfo?access_token='
            . $request->get('accessToken'))->getBody()->getContents();
            $response_decoded = json_decode($response, true);
            if ($response_decoded['user_id'] == $request->get('extern_id')) {
                $socialLink = SocialLink::where([
                    'extern_id' => $request->get('extern_id'), 'source' => 'google'
                ])->first();
                if ($socialLink) {
                    $user = User::find($socialLink['user_id']);
                    $sesion['id'] = $user['id'];
                    $sesion['token'] = $user
                    ->createToken(env('APP_OAUTH_PASS', 'OAuth'))->accessToken;
                    return response()->json($sesion, 200);
                } else {
                    return response()->json('SERVER.USER_NOT_REGISTRED', 404);
                }
            } else {
                return response()->json('SERVER.WRONG_USER', 404);
            }
        } catch (\GuzzleHttp\Exception\ClientException $error) {
            return response()->json('SERVER.WRONG_TOKEN', 406);
        }
    }

    private function facebookLogin(Request $request) {
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->get('https://graph.facebook.com/me?fields=id&access_token='
            . $request->get('accessToken'))->getBody()->getContents();
            $response_decoded = json_decode($response, true);
            if ($response_decoded['id'] == $request->get('extern_id')) {
                $socialLink = SocialLink::where([
                    'extern_id' => $request->get('extern_id'), 'source' => 'facebook'
                ])->first();
                if ($socialLink) {
                    $user = User::find($socialLink['user_id']);
                    $sesion['id'] = $user['id'];
                    $sesion['token'] = $user
                    ->createToken(env('APP_OAUTH_PASS', 'OAuth'))->accessToken;
                    return response()->json($sesion, 200);
                } else {
                    return response()->json('SERVER.USER_NOT_REGISTRED', 404);
                }
            } else {
                return response()->json('SERVER.WRONG_USER', 404);
            }
        } catch (\GuzzleHttp\Exception\ClientException $error) {
            return response()->json('SERVER.WRONG_TOKEN', 406);
        }
    }
}
