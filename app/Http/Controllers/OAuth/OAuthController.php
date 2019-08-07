<?php

namespace App\Http\Controllers\OAuth;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User\User;
use App\Models\User\User\SocialLink;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Traits\PassportToken;
use Laravel\Passport\Client;

class OAuthController extends BaseController
{
    use PassportToken;
    
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
        $client = $this->checkClient($request);
        if ($client) {
            switch ($request->get('grant_type')) {
                case 'password': {
                    return $this->passwordLogin($request, $client);
                } break;
                case 'google': {
                    return $this->googleLogin($request, $client);
                } break;
                case 'facebook': {
                    return $this->facebookLogin($request, $client);
                } break;
                default: {
                    return response()->json('SERVER.INCORRECT_CREDENTIALS', 406);
                }
            }
        } else {
            return response()->json([
                "error" => "invalid_client",
                "message" => "Client authentication failed"
            ], 401);
        }
    }

    /**
     * Verifica las credenciales del usuario con email y contraseña.
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  Laravel\Passport\Client $client
     * @return \Illuminate\Http\Response
     */
    private function passwordLogin(Request $request, Client $client) {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6|max:60',
        ]);
        $user = User::where([
            'email' => $request->get('email'), 'grant_type' => $request->get('grant_type')
        ])->first();
        if ($user && Hash::check($request->get('password'), $user['password'])) {
            return $this->getBearerTokenByUser($user, $client['id'], true);
        } else {
            return response()->json('SERVER.INCORRECT_USER', 406);
        }
    }

    /**
     * Verifica las credenciales del ususario comprobando con google.
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  Laravel\Passport\Client $client
     * @return \Illuminate\Http\Response
     */
    private function googleLogin(Request $request, Client $client) {
        $guzzleClient = new \GuzzleHttp\Client();
        try {
            $response = $guzzleClient->get('https://www.googleapis.com/oauth2/v1/tokeninfo?access_token='
            . $request->get('accessToken'))->getBody()->getContents();
            $response_decoded = json_decode($response, true);
            if ($response_decoded['user_id'] == $request->get('extern_id')) {
                $socialLink = SocialLink::where([
                    'extern_id' => $request->get('extern_id'), 'grant_type' => 'google'
                ])->first();
                if ($socialLink) {
                    $user = User::find($socialLink['user_id']);
                    return $this->getBearerTokenByUser($user, $client['id'], true);
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

    /**
     * Verifica las credenciales del ususario comprobando con facebook.
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  Laravel\Passport\Client $client
     * @return \Illuminate\Http\Response
     */
    private function facebookLogin(Request $request, Client $client) {
        $guzzleClient = new \GuzzleHttp\Client();
        try {
            $response = $guzzleClient->get('https://graph.facebook.com/me?fields=id&access_token='
            . $request->get('accessToken'))->getBody()->getContents();
            $response_decoded = json_decode($response, true);
            if ($response_decoded['id'] == $request->get('extern_id')) {
                $socialLink = SocialLink::where([
                    'extern_id' => $request->get('extern_id'), 'grant_type' => 'facebook'
                ])->first();
                if ($socialLink) {
                    $user = User::find($socialLink['user_id']);
                    return $this->getBearerTokenByUser($user, $client['id'], true);
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
