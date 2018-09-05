<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;
use App\Models\EmailConfirm;
use App\Models\Direction;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;
use App\Notifications\RegistrationConfirmation;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;

class UserController extends BaseController
{
    /**
     * Recupera la información básica de un usuario.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getUser(Request $request) {
        $user = $request->user();
        if ($user) {
            if ($user['direction_id']) {
                $user['direction'] = Direction::find($user['direction_id']);
            }
            return response()->json($user, 200);
        } else {
            return response()->json('SERVER.USER_NOT_FOUND', 404);
        }
    }
    /**
     * Recupera la información básica de un usuario.
     *
     * @param  number $id
     * @return \Illuminate\Http\Response
     */
    public function getUserById($id) {
        $user = User::select(
            'id',
            'name',
            'first_name',
            'last_name',
            'gender',
            'email',
            'img_url',
            'source',
            'phone',
            'lang',
            'birthday',
            'role',
            'direction_id'
        )->find($id);
        if ($user) {
            if ($user['direction_id']) {
                $user['direction'] = Direction::find($user['direction_id']);
            }
            return response()->json($user, 200);
        } else {
            return response()->json('SERVER.USER_NOT_REGISTRED', 404);
        }
    }
    /**
     * Recupera la información básica de varios usuarios.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getUsers(Request $request) {
        $user = User::select(
            'id',
            'name',
            'first_name',
            'last_name',
            'gender',
            'email',
            'img_url',
            'users.source',
            'phone',
            'lang',
            'birthday',
            'role'
        )->where('name', 'like', '%' . $request->get('search') . '%')
        ->orWhere('email', 'like', '%' . $request->get('search') . '%')
        ->orWhere('phone', 'like', '%' . $request->get('search') . '%')
        ->paginate(15);

        return response()->json($user, 200);
    }
    /**
     * Valida e inserta los datos del usuario.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function signup(Request $request) {
        $this->validate($request, [
            'name' => 'required|min:4|max:60',
            'first_name' => 'required|max:60',
            'last_name' => 'required|max:60',
            'email' => 'required|email',
            'lang' => 'required',
            'source' => 'required'
        ]);
        if ($request->isJson()) {
            try {
                if ($request->get('source') == 'app') {
                    $user = User::where(['source' => $request->get('source'), 'email' => $request->get('email')])->first();
                    if ($user) {
                        return response()->json('SERVER.USER_ALREADY_EXISTS', 401);
                    }
                    $this->validate($request, [
                        'password' => 'required|min:6|max:60'
                    ]);
                    $user = User::create([
                        'name' => $request->get('name'),
                        'first_name' => $request->get('first_name'),
                        'last_name' => $request->get('last_name'),
                        'gender' => $request->get('gender'),
                        'email' => $request->get('email'),
                        'password' => Hash::make($request->get('password')),
                        'img_url' => $request->get('img_url'),
                        'lang' => $request->get('lang'),
                        'source' => $request->get('source')
                    ]);
                    $sesion['id'] = $user['id'];
                    $sesion['token'] = $user->createToken('BigThinks')->accessToken; 
                } else {
                    $user = User::where(['source' => $request->get('source'), 'extern_id' => $request->get('extern_id')])->first();
                    if ($user) {
                        return response()->json('SERVER.USER_ALREADY_EXISTS', 401);
                    }
                    $user = User::create([
                        'name' => $request->get('name'),
                        'first_name' => $request->get('first_name'),
                        'last_name' => $request->get('last_name'),
                        'gender' => $request->get('gender'),
                        'email' => $request->get('email'),
                        'img_url' => $request->get('img_url'),
                        'lang' => $request->get('lang'),
                        'source' => $request->get('source'),
                        'extern_id' => $request->get('extern_id')
                    ]);
                    $sesion['id'] = $user['id'];
                    $sesion['token'] = $user->createToken('BigThinks')->accessToken;  
                }
                $this->sendConfirmEmail($user);
                return response()->json($sesion, 201);
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
    public function login(Request $request) {
        if ($request->isJson()) {
            try {
                switch ($request->get('source')) {
                    case 'app': {
                        $user = User::where('email', $request->get('email'))->where('source', $request->get('source'))->first();
                        if ($user && Hash::check($request->get('password'), $user->password)) {
                            $sesion['id'] = $user['id'];
                            $sesion['token'] = $user->createToken('BigThinks')->accessToken;
                            return response()->json($sesion, 200);
                        } else {
                            return response()->json('SERVER.INCORRECT_USER', 406);
                        }
                    }
                    break;
                    case 'facebook': {
                        $client = new \GuzzleHttp\Client();
                        try {
                            $response = $client->get('https://graph.facebook.com/me?fields=id&access_token='
                            . $request->get('accessToken'))->getBody()->getContents();
                            $response_decoded = json_decode($response, true);
                            if ($response_decoded['id'] == $request->get('extern_id')) {
                                $user = User::where('extern_id', $request->get('extern_id'))->first();
                                if ($user) {
                                    $sesion['id'] = $user['id'];
                                    $sesion['token'] = $user->createToken('BigThinks')->accessToken;
                                    return response()->json($sesion, 200);
                                } else {
                                    return response()->json('SERVER.USER_NOT_REGISTRED', 404);
                                }
                            } else {
                                return response()->json('SERVER.WRONG_USER', 406);
                            }
                        } catch (\GuzzleHttp\Exception\ClientException $error) {
                            return response()->json('SERVER.WRONG_TOKEN', 406);
                        }
                    }
                    break;
                    case 'google': {
                        $client = new \GuzzleHttp\Client();
                        try {
                            $response = $client->get('https://www.googleapis.com/oauth2/v1/tokeninfo?access_token='
                            . $request->get('accessToken'))->getBody()->getContents();
                            $response_decoded = json_decode($response, true);
                            if ($response_decoded['user_id'] == $request->get('extern_id')) {
                                $user = User::where('extern_id', $request->get('extern_id'))->first();
                                if ($user) {
                                    $sesion['id'] = $user['id'];
                                    $sesion['token'] = $user->createToken('BigThinks')->accessToken;
                                    return response()->json($sesion, 200);
                                } else {
                                    return response()->json('SERVER.USER_NOT_REGISTRED', 404);
                                }
                            } else {
                                return response()->json('SERVER.WRONG_USER', 406);
                            }
                        } catch (\GuzzleHttp\Exception\ClientException $error) {
                            return response()->json('SERVER.WRONG_TOKEN', 406);
                        }
                    }
                    break;
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
    public function reSendConfirmEmail(Request $request) {
        $this->validate($request, [
            'id' => 'required'
        ]);
        if ($request->isJson()) {
            try {
                $user = User::where('id', $request->get('id'))->first();
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
    public function sendConfirmEmail($user) {
        $emailConfirmData = EmailConfirm::where('user_id', $user['id'])->first();
        if ($emailConfirmData == '') {
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
        $response = $user->notify(new RegistrationConfirmation($confirmationLink, $user['lang']));
        if ($response == '') {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }
    /**
     * Actualiza la información del usuario para confirmar el correo electrónico.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function confirmEmail(Request $request) {
        $this->validate($request, [
            'token' => 'required'
        ]);
        $emailConfirmData = EmailConfirm::where('token', $request->token)->first();
        if ($emailConfirmData == '') {
            return response()->json('SERVER.WRONG_TOKEN', 406);
        } else {
            $dateTimeNow = Carbon::now();
            $dateTimeCreatedAt = Carbon::parse($emailConfirmData->created_at);
            if ($dateTimeNow->diffInDays($dateTimeCreatedAt) <= 30) {
                $user = User::where('id', $emailConfirmData->user_id)->first();
                $user->confirmed = true;
                $user->save();
                $emailConfirmData->delete();
                // TODO Actualizar al instalarlo
                return redirect(env('APP_REDIRECTS_LINK', '../'));
            } else {
                $emailConfirmData->delete();
                return response()->json('SERVER.TOKEN_EXPIRED', 406);
            }
        }
    }
    /**
     * Actualizar la dirección del usuario.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateUserDirection(Request $request) {
        try {
            $user = $request->user();
            $userDirection = Direction::find($user['direction_id']);
            if ($userDirection) {
                if ($request->get('country')) {
                    $this->validate($request, ['country' => 'required|max:60',]);
                    $userDirection['country'] = $request->get('country');
                }
                if ($request->get('administrative_area_level_1')) {
                    $this->validate($request, ['administrative_area_level_1' => 'required|max:60',]);
                    $userDirection['administrative_area_level_1'] = $request->get('administrative_area_level_1');
                }
                if ($request->get('administrative_area_level_2')) {
                    $this->validate($request, ['administrative_area_level_2' => 'required|max:60',]);
                    $userDirection['administrative_area_level_2'] = $request->get('administrative_area_level_2');
                }
                if ($request->get('route')) {
                    $this->validate($request, ['route' => 'required|max:60',]);
                    $userDirection['route'] = $request->get('route');
                }
                if ($request->get('street_number')) {
                    $this->validate($request, ['street_number' => 'required',]);
                    $userDirection['street_number'] = $request->get('street_number');
                }
                if ($request->get('postal_code')) {
                    $this->validate($request, ['postal_code' => 'required|numeric',]);
                    $userDirection['postal_code'] = $request->get('postal_code');
                }
                if ($request->get('lat')) {
                    $this->validate($request, ['lat' => 'required|numeric',]);
                    $userDirection['lat'] = $request->get('lat');
                }
                if ($request->get('lng')) {
                    $this->validate($request, ['lng' => 'required|numeric',]);
                    $userDirection['lng'] = $request->get('lng');
                }
                $userDirection->save();
            } else {
                $userDirection = Direction::create([
                    'country' => $request->get('country'),
                    'administrative_area_level_1' => $request->get('administrative_area_level_1'),
                    'administrative_area_level_2' => $request->get('administrative_area_level_2'),
                    'route' => $request->get('route'),
                    'street_number' => $request->get('street_number'),
                    'postal_code' => $request->get('postal_code'),
                    'lat' => $request->get('lat'),
                    'lng' => $request->get('lng'),
                ]);
                $user['direction_id'] = $userDirection['id'];
                $user->save();
            }
            return response()->json($userDirection, 201);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Actualiza la información básica de usuario.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request) {
        try {
            $user = $request->user();
            if ($request->get('name')) {
                $this->validate($request, ['name' => 'required|min:4|max:60',]);
                $user->name = $request->get('name');
            }
            if ($request->get('first_name')) {
                $this->validate($request, ['first_name' => 'required|max:60',]);
                $user->first_name = $request->get('first_name');
            }
            if ($request->get('last_name')) {
                $this->validate($request, ['last_name' => 'required|max:60',]);
                $user->last_name = $request->get('last_name');
            }
            if ($request->get('gender')) {
                $this->validate($request, ['gender' => 'required|string',]);
                $user->gender = $request->get('gender');
            }
            if ($request->get('phone')) {
                $this->validate($request, ['phone' => 'required|numeric',]);
                $user->phone = $request->get('phone');
            }
            if ($request->get('birthday')) {
                $user->birthday = $request->get('birthday');
            }
            $user->save();
            return response()->json($user, 201);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Llama la función para enviar un correo de confirmación.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateUserEmail(Request $request) {
        try {
            $user = $request->user();
            if ($request->get('email')) {
                $this->validate($request, [
                    'email' => 'required|email',
                    'source' => 'required',
                ]);
                // VALIDAMOS QUE NO EXISTA EL USUARIO DEL MISMO SOURCE
                $validate = User::where([
                    'email' => $request->get('email'),
                    'source' => $request->get('source')
                ])->first();
                if ($validate) {
                    return response()->json('SERVER.USER_EMAIL_ALREADY_EXISTS', 406);
                } else {
                    $user->email = $request->get('email');
                    $user->save();
                }
            }
            return response()->json($user, 201);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Para actualizar el idioma de usuario registrado
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateUserLang(Request $request) {
        $this->validate($request, [
            'lang' => 'required',
        ]);
        $user = $request->user();
        if ($user) {
            $user['lang'] = $request->get('lang');
            $user->save();
            return response()->json($user['lang'], 202);
        }
        return response()->json('SERVER.USER_NOT_REGISTRED', 404);
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
        $path = $_SERVER['DOCUMENT_ROOT'] . '/big_thinks_back/img/users_avatars/';
        $file_url = URL::to('/') . '/img/users_avatars/' . $file_name;
        if (!File::exists($path)) {
            File::makeDirectory($path, 0775, true);
        }
        $user = $request->user();
        Image::make($file)->save($path . $file_name);
        // Evalúa si hay un archivo registrado en el servidor con el mismo nombre para eliminarlo.
        if ($user->img_url && parse_url($user->img_url)['host'] == parse_url(URL::to('/'))['host']) {
            File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($user->img_url)['path']);
        }
        $user->img_url = $file_url;
        $user->save();
        return response()->json($file_url, 202);
    }
    /**
     * Actualizar la dirección del usuario.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  number $id
     * @return \Illuminate\Http\Response
     */
    public function updateUserDirectionById(Request $request, $id) {
        try {
            $user = User::find($id);
            $userDirection = Direction::find($user['direction_id']);
            if ($userDirection) {
                if ($request->get('country')) {
                    $this->validate($request, ['country' => 'required|max:60',]);
                    $userDirection['country'] = $request->get('country');
                }
                if ($request->get('administrative_area_level_1')) {
                    $this->validate($request, ['administrative_area_level_1' => 'required|max:60',]);
                    $userDirection['administrative_area_level_1'] = $request->get('administrative_area_level_1');
                }
                if ($request->get('administrative_area_level_2')) {
                    $this->validate($request, ['administrative_area_level_2' => 'required|max:60',]);
                    $userDirection['administrative_area_level_2'] = $request->get('administrative_area_level_2');
                }
                if ($request->get('route')) {
                    $this->validate($request, ['route' => 'required|max:60',]);
                    $userDirection['route'] = $request->get('route');
                }
                if ($request->get('street_number')) {
                    $this->validate($request, ['street_number' => 'required',]);
                    $userDirection['street_number'] = $request->get('street_number');
                }
                if ($request->get('postal_code')) {
                    $this->validate($request, ['postal_code' => 'required|numeric',]);
                    $userDirection['postal_code'] = $request->get('postal_code');
                }
                if ($request->get('lat')) {
                    $this->validate($request, ['lat' => 'required|numeric',]);
                    $userDirection['lat'] = $request->get('lat');
                }
                if ($request->get('lng')) {
                    $this->validate($request, ['lng' => 'required|numeric',]);
                    $userDirection['lng'] = $request->get('lng');
                }
                $userDirection->save();
            } else {
                $userDirection = Direction::create([
                    'country' => $request->get('country'),
                    'administrative_area_level_1' => $request->get('administrative_area_level_1'),
                    'administrative_area_level_2' => $request->get('administrative_area_level_2'),
                    'route' => $request->get('route'),
                    'street_number' => $request->get('street_number'),
                    'postal_code' => $request->get('postal_code'),
                    'lat' => $request->get('lat'),
                    'lng' => $request->get('lng'),
                ]);
                $user['direction_id'] = $userDirection['id'];
                $user->save();
            }
            return response()->json($userDirection, 201);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Actualiza la información básica de usuario.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  number $id
     * @return \Illuminate\Http\Response
     */
    public function updateUserById(Request $request, $id) {
        try {
            $user = User::find($id);
            if ($request->get('name')) {
                $this->validate($request, ['name' => 'required|min:4|max:60',]);
                $user->name = $request->get('name');
            }
            if ($request->get('first_name')) {
                $this->validate($request, ['first_name' => 'required|max:60',]);
                $user->first_name = $request->get('first_name');
            }
            if ($request->get('last_name')) {
                $this->validate($request, ['last_name' => 'required|max:60',]);
                $user->last_name = $request->get('last_name');
            }
            if ($request->get('gender')) {
                $this->validate($request, ['gender' => 'required|string',]);
                $user->gender = $request->get('gender');
            }
            if ($request->get('phone')) {
                $this->validate($request, ['phone' => 'required|numeric',]);
                $user->phone = $request->get('phone');
            }
            if ($request->get('birthday')) {
                $user->birthday = $request->get('birthday');
            }
            $user->save();
            return response()->json($user, 201);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Llama la función para enviar un correo de confirmación.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  number $id
     * @return \Illuminate\Http\Response
     */
    public function updateUserEmailById(Request $request, $id) {
        try {
            $user = User::find($id);
            if ($request->get('email')) {
                $this->validate($request, [
                    'email' => 'required|email',
                    'source' => 'required',
                ]);
                // VALIDAMOS QUE NO EXISTA EL USUARIO DEL MISMO SOURCE
                $validate = User::where([
                    'email' => $request->get('email'),
                    'source' => $request->get('source')
                ])->first();
                if ($validate) {
                    return response()->json('SERVER.USER_EMAIL_ALREADY_EXISTS', 406);
                } else {
                    $user->email = $request->get('email');
                    $user->save();
                }
            }
            return response()->json($user, 201);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Para actualizar el idioma de usuario registrado
     *
     * @param  \Illuminate\Http\Request $request
     * @param  number $id
     * @return \Illuminate\Http\Response
     */
    public function updateUserLangById(Request $request, $id) {
        $this->validate($request, [
            'lang' => 'required',
        ]);
        $user = User::find($id);
        if ($user) {
            $user['lang'] = $request->get('lang');
            $user->save();
            return response()->json($user['lang'], 202);
        }
        return response()->json('SERVER.USER_NOT_REGISTRED', 404);
    }
    /**
     * guarda un archivo en nuestro directorio local.
     * 
     * @param \Illuminate\Http\Request $request
     * @param number $id
     * @return \Illuminate\Http\Response
     */
    public function saveAvatarById(Request $request, $id)
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
        $path = $_SERVER['DOCUMENT_ROOT'] . '/big_thinks_back/img/users_avatars/';
        $file_url = URL::to('/') . '/img/users_avatars/' . $file_name;
        if (!File::exists($path)) {
            File::makeDirectory($path, 0775, true);
        }
        $user = User::find($id);
        Image::make($file)->save($path . $file_name);
        // Evalúa si hay un archivo registrado en el servidor con el mismo nombre para eliminarlo.
        if ($user['img_url'] && parse_url($user['img_url'])['host'] == parse_url(URL::to('/'))['host']) {
            File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($user['img_url'])['path']);
        }
        $user['img_url'] = $file_url;
        $user->save();
        return response()->json($file_url, 202);
    }
    /**
     * Estable el rol del usuario
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function setUserRoleById(Request $request, $id) {
        $this->validate($request, [
            'role' => 'required',
        ]);
        $user = User::find($id);
        if ($user) {
            $user['role'] = $request->get('role');
            $user->save();
            return response()->json($user['role'], 202);
        }
        return response()->json('SERVER.USER_NOT_REGISTRED', 404);
    }
    /**
     * Estable el rol del usuario
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) {
        $request->user()->token()->revoke();
        $request->user()->token()->delete();
        return response()->json('SERVER.LOGGEDOUT', 200);
    }
    /**
     * Elimina un usuario
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteUser(Request $request) {
        $user = $request->user();
        if ($user) {
            $request->user()->token()->revoke();
            $request->user()->token()->delete();
            if ($user['direction_id']) {
                Direction::find($user['direction_id'])->delete();
            }
            if ($user['img_url'] && parse_url($user['img_url'])['host'] == parse_url(URL::to('/'))['host']) {
                File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($user['img_url'])['path']);
            }
            $user->delete();
            return response()->json('SERVER.USER_DELETED', 200);
        } else {
            return response()->json('SERVER.USER_NOT_FOUND', 404);
        }
    }
    /**
     * Elimina un usuario
     *
     * @param  number $id
     * @return \Illuminate\Http\Response
     */
    public function deleteUserById($id) {
        $user = User::find($id);
        Direction::find($user['direction_id'])->delete();
        $user->delete();
        return response()->json('SERVER.USER_DELETED', 200);
    }
}
