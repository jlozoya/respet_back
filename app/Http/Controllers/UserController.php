<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;
use App\Models\EmailConfirm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;
use App\Notifications\RegistrationConfirmation;
use Carbon\Carbon;

class UserController extends BaseController
{
    /**
     * Valida e inserta los datos del usuario.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function signup(Request $request) {
        $this->validate($request, [
            'name' => 'required|min:5|max:60',
            'first_name' => 'required|max:60',
            'last_name' => 'required|max:60',
            'email' => 'required|email',
            'source' => 'required'
        ]);
        if ($request->isJson()) {
            try {
                $data = $request->json()->all();
                if ($data['source'] == 'app') {
                    $this->validate($request, [
                        'password' => 'required|min:6|max:60'
                    ]);
                    $user = User::create([
                        'name' => $data['name'],
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'gender' => $data['gender'],
                        'email' => $data['email'],
                        'password' => Hash::make($data['password']),
                        'Authorization' => str_random(60),
                        'img_url' => $data['img_url'],
                        'source' => $data['source']
                    ]);
                } else if ($data['source'] == 'facebook') {
                    $user = User::create([
                        'name' => $data['name'],
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'gender' => $data['gender'],
                        'email' => $data['email'],
                        'Authorization' => str_random(60),
                        'img_url' => $data['img_url'],
                        'source' => $data['source'],
                        'extern_id' => $data['extern_id']
                    ]);
                }
                $this->sendConfirmEmail($user);
                return response()->json($user, 201);
            } catch (Illuminate\Database\QueryException $error) {
                return response()->json($error, 406);
            }
        }
        return response()->json('SERVER.UNAUTHORIZED', 401);
    }
    /**
     * Valida las credenciales del usuario en caso de ser correctas devuelve la demás información del usuario.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function login(Request $request) {
        if ($request->isJson()) {
            try {
                $data = $request->json()->all();
                if ($data['source'] == 'app') {
                    $user = User::where('email', $data['email'])->first();
                    if ($user && Hash::check($data['password'], $user->password)) {
                        return response()->json($user, 200);
                    } else {
                        return response()->json('SERVER.INCORRECT_USER', 406);
                    }
                } else if ($data['source'] == 'facebook') {
                    $client = new \GuzzleHttp\Client();
                    try {
                        $response = $client->get('https://graph.facebook.com/me?fields=id&access_token=' . $data['accessToken'])->getBody()->getContents();
                        $response_decoded = json_decode($response, true);
                        if ($response_decoded['id'] == $data['extern_id']) {
                            $user = User::where('extern_id', $data['extern_id'])->first();
                            if ($user) {
                                return response()->json($user, 200);
                            } else {
                                return response()->json('SERVER.USER_NOT_REGISTRED', 200);
                            }
                        } else {
                            return response()->json('SERVER.WRONG_USER', 406);
                        }
                    } catch (\GuzzleHttp\Exception\ClientException $error) {
                        return response()->json('SERVER.WRONG_TOKEN', 406);
                    }
                }
            } catch (ModelNotFoundException $error) {
                return response()->json('SERVER.WRONG_USER', 406);
            }
        }
        return response()->json('SERVER.UNAUTHORIZED', 401);
    }
    /**
     * Llama la función para enviar un correo de confirmación.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function reSendConfirmEmail(Request $request) {
        $this->validate($request, [
            'id' => 'required'
        ]);
        if ($request->isJson()) {
            try {
                $data = $request->json()->all();
                $user = User::where('id', $data['id'])->first();
                if (!$user->confirmed) {
                    return $this->sendConfirmEmail($user);
                } else {
                    return response()->json('SERVER.USER_ALREADY_CONFIRMED', 200);
                }
            } catch (ModelNotFoundException $error) {
                return response()->json('SERVER.WRONG_USER', 406);
            }
        }
    }
    /**
     * Envía el correo electrónico correspondiente para que el usuario confirme que es su correo electrónico.
     *
     * @param $user
     * @return \Illuminate\Http\Response
     */
    function sendConfirmEmail($user) {
        $emailConfirmData = EmailConfirm::where('user_id', $user['id'])->first();
        if ($emailConfirmData == "") {
            $emailConfirmData = EmailConfirm::create([
                'user_id' => $user['id'],
                'email' => $user['email'],
                'token' => str_random(60)
            ]);
        } else {
            $emailConfirmData->token = str_random(60);
            $emailConfirmData->save();
        }
        $confirmationLink = route('user.confirm.email') . '?token=' . $emailConfirmData->token;
        $response = $user->notify(new RegistrationConfirmation($confirmationLink));
        if ($response == '') {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }
    /**
     * Recupera los datos de las compañías del usuario.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function getUserCompanies(Request $request) {
        $user_role_companies = User::select('companies.*', 'user_roles.*')
            ->where('Authorization', $request->header('Authorization'))
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->join('companies', 'user_roles.company_id', '=', 'companies.id')
            ->get();
        return $user_role_companies;
    }
    /**
     * Actualiza la información del usuario para confirmar el correo electrónico.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function confirmEmail(Request $request) {
        $this->validate($request, [
            'token' => 'required'
        ]);
        $emailConfirmData = EmailConfirm::where('token', $request->token)->first();
        if ($emailConfirmData == "") {
            return response()->json('SERVER.WRONG_TOKEN', 406);
        } else {
            $dateTimeNow = Carbon::now();
            $dateTimeCreatedAt = Carbon::parse($emailConfirmData->created_at);
            if ($dateTimeNow->diffInDays($dateTimeCreatedAt) <= 30) {
                $user = User::where('id', $emailConfirmData->user_id)->first();
                $user->confirmed = true;
                $user->save();
                $emailConfirmData->delete();
                return response()->json('SERVER.EMAIL_CONFIRMED', 406);
            } else {
                $emailConfirmData->delete();
                return response()->json('SERVER.TOKEN_EXPIRED', 406);
            }
        }
    }
    /**
     * guarda un archivo en nuestro directorio local.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function saveAvatar(Request $request)
    {
        $this->validate($request, [
            'file_name' => 'required',
            'type' => 'required'
        ]);
        $file_name = $request->get('file_name');
        if ($request['type'] == 'base64') {
            $file = base64_decode(explode(',', $request['file'])[1]);
        } else {
            $file = $request->file('file');
        }
        $path = $_SERVER['DOCUMENT_ROOT'] . '/perrosdelagua_back/img/users_avatars/';
        $file_url = URL::to('/') . '/img/users_avatars/' . $file_name;
        if (!File::exists($path)) {
            File::makeDirectory($path, 0775, true);
        }
        $user = User::where('Authorization', $request->header('Authorization'))->first();
        Image::make($file)->save($path . $file_name);
        // Evalúa si hay un archivo registrado en el servidor con el mismo nombre para eliminarlo.
        if (parse_url($user->img_url)['host'] == parse_url(URL::to('/'))['host']) {
            File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($user->img_url)['path']);
        }
        $user->img_url = $file_url;
        $user->save();
        return response()->json($file_url, 202);
    }
}
