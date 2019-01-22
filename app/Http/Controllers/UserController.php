<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;
use App\Models\EmailConfirm;
use App\Models\Direction;
use App\Models\Media;
use App\Models\SocialLink;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Auth;

use App\Notifications\RegistrationConfirmation;

use Carbon\Carbon;

class UserController extends BaseController
{
    /**
     * Recupera la información básica de un usuario.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $user = $request->user();
        if ($user) {
            if ($user['direction_id']) {
                $user['direction'] = Direction::find($user['direction_id']);
            }
            if ($user['media_id']) {
                $user['media'] = Media::find($user['media_id']);
            }
            $user['social_links'] = SocialLink::select(
                'id',
                'extern_id',
                'grant_type'
            )->where('user_id', $user['id'])->get();
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
            'media_id',
            'grant_type',
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
            if ($user['media_id']) {
                $user['media'] = Media::find($user['media_id']);
            }
            $user['social_links'] = SocialLink::select(
                'id',
                'extern_id',
                'grant_type'
            )->where('user_id', $user['id'])->get();
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
        $users = User::select(
            'id',
            'name',
            'first_name',
            'last_name',
            'gender',
            'email',
            'media_id',
            'grant_type',
            'phone',
            'lang',
            'birthday',
            'role'
        )->where('name', 'like', '%' . $request->get('search') . '%')
        ->orWhere('email', 'like', '%' . $request->get('search') . '%')
        ->orWhere('phone', 'like', '%' . $request->get('search') . '%')
        ->paginate(15);
        foreach ($users as &$user) {
            if ($user['direction_id']) {
                $user['direction'] = Direction::find($user['direction_id']);
            }
            if ($user['media_id']) {
                $user['media'] = Media::find($user['media_id']);
            }
            $user['social_links'] = SocialLink::select(
                'id',
                'extern_id',
                'grant_type'
            )->where('user_id', $user['id'])->get();
        }
        return response()->json($users, 200);
    }
    /**
     * Valida e inserta los datos del usuario.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|min:4|max:60',
            'first_name' => 'required|max:60',
            'last_name' => 'required|max:60',
            'email' => 'required|email',
            'lang' => 'required',
            'grant_type' => 'required'
        ]);
        $error = $this->checkClient($request);
        if ($error) {
            return $error;
        }
        if ($request->get('grant_type') == 'app') {
            $user = User::where([
                'grant_type' => $request->get('grant_type'), 'email' => $request->get('email')
            ])->first();
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
                'lang' => $request->get('lang'),
                'grant_type' => $request->get('grant_type'),
            ]);
            if ($request->get('media')) {
                $media = Media::create([
                    'url' => $request->get('media')['url'],
                    'alt' => $request->get('media')['alt'],
                    'width' => $request->get('media')['width'],
                    'height' => $request->get('media')['height'],
                ]);
                $user['media_id'] = $media['id'];
                $user->save();
            }
            $sesion['id'] = $user['id'];
            $sesion['token'] = $user->createToken(env('APP_OAUTH_PASS', 'OAuth'))->accessToken; 
        } else {
            $socialLink = SocialLink::where([
                'grant_type' => $request->get('grant_type'), 'extern_id' => $request->get('extern_id')
            ])->first();
            if ($socialLink) {
                return response()->json('SERVER.USER_ALREADY_EXISTS', 401);
            }
            $user = User::create([
                'name' => $request->get('name'),
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'gender' => $request->get('gender'),
                'email' => $request->get('email'),
                'lang' => $request->get('lang'),
                'grant_type' => $request->get('grant_type'),
            ]);
            $socialLink = SocialLink::create([
                'user_id' => $user['id'],
                'grant_type' => $request->get('grant_type'),
                'extern_id' => $request->get('extern_id'),
            ]);
            if ($request->get('media')) {
                $media = Media::create([
                    'url' => $request->get('media')['url'],
                    'alt' => 'avatar',
                ]);
                $user['media_id'] = $media['id'];
                $user->save();
            }
            $sesion['id'] = $user['id'];
            $sesion['token'] = $user
            ->createToken(env('APP_OAUTH_PASS', 'OAuth'))->accessToken;  
        }
        $this->sendConfirmEmail($user);
        return response()->json($sesion, 201);
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
        $user = User::find($request->get('id'));
        if ($user) {
            if (!$user['confirmed']) {
                return $this->sendConfirmEmail($user);
            } else {
                return response()->json('SERVER.USER_ALREADY_CONFIRMED', 200);
            }
        }
        return response()->json('SERVER.USER_NOT_FOUND', 404);
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
            $emailConfirmData['token'] = str_random(60);
            $emailConfirmData->save();
        }
        $confirmationLink = route('user.confirm.email') . '?token=' . $emailConfirmData['token'];
        $response = $user->notify(new RegistrationConfirmation($confirmationLink, $user['lang']));
        if ($response == '') {
            return response()->json('SERVER.EMAIL_SEND', 200);
        } else {
            return response()->json('SERVER.EMAIL_FAIL', 400);
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
        $emailConfirmData = EmailConfirm::where('token', $request->get('token'))->first();
        if ($emailConfirmData) {
            $dateTimeNow = Carbon::now();
            $dateTimeCreatedAt = Carbon::parse($emailConfirmData['created_at']);
            if ($dateTimeNow->diffInDays($dateTimeCreatedAt) <= 30) {
                $user = User::where('id', $emailConfirmData['user_id'])->first();
                $user['confirmed'] = true;
                $user->save();
                $emailConfirmData->delete();
                return redirect(env('APP_REDIRECTS_LINK', '../'));
            } else {
                $emailConfirmData->delete();
                return response()->json('SERVER.TOKEN_EXPIRED', 406);
            }
        } else {
            return response()->json('SERVER.WRONG_TOKEN', 406);
        }
    }
    /**
     * Vincula una red social a una cuenta.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function createSocialLink(Request $request) {
        try {
            $user = $request->user();
            $this->validate($request, [
                'grant_type' => 'required',
                'extern_id' => 'required',
                'accessToken' => 'required'
            ]);
            switch ($request->get('grant_type')) {
                case 'google': {
                    $client = new \GuzzleHttp\Client();
                    try {
                        $response = $client->get('https://www.googleapis.com/oauth2/v1/tokeninfo?access_token='
                        . $request->get('accessToken'))->getBody()->getContents();
                        $response_decoded = json_decode($response, true);
                        if ($response_decoded['user_id'] == $request->get('extern_id')) {
                            $socialLink = SocialLink::where([
                                'extern_id' => $request->get('extern_id'),
                                'grant_type' => $request->get('grant_type')
                            ])->first();
                            if (!$socialLink && $user['grant_type'] != $request->get('grant_type')) {
                                $socialLink = SocialLink::create([
                                    'user_id' => $user['id'],
                                    'extern_id' => $request->get('extern_id'),
                                    'grant_type' => $request->get('grant_type')
                                ]);
                                return response()->json($socialLink, 202);
                            } else {
                                return response()->json('SERVER.USER_SOCIAL_ALREADY_USED', 401);
                            }
                        } else {
                            return response()->json('SERVER.WRONG_USER', 404);
                        }
                    } catch (\GuzzleHttp\Exception\ClientException $error) {
                        return response()->json('SERVER.WRONG_TOKEN', 406);
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
                            $socialLink = SocialLink::where([
                                'extern_id' => $request->get('extern_id'),
                                'grant_type' => $request->get('grant_type')
                            ])->first();
                            if (!$socialLink && $user['grant_type'] != $request->get('grant_type')) {
                                $socialLink = SocialLink::create([
                                    'user_id' => $user['id'],
                                    'extern_id' => $request->get('extern_id'),
                                    'grant_type' => $request->get('grant_type')
                                ]);
                                return response()->json($socialLink, 202);
                            } else {
                                return response()->json('SERVER.USER_SOCIAL_ALREADY_USED', 404);
                            }
                        } else {
                            return response()->json('SERVER.WRONG_USER', 404);
                        }
                    } catch (\GuzzleHttp\Exception\ClientException $error) {
                        return response()->json('SERVER.WRONG_TOKEN', 406);
                    }
                }
                break;
            }
        } catch (ModelNotFoundException $error) {
            return response()->json('SERVER.WRONG_USER', 404);
        }
    }
    /**
     * Vincula una red social a una cuenta.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteSocialLink(Request $request, $id) {
        $user = $request->user();
        $socialLink = SocialLink::where([
            'id' => $id, 'user_id' => $user['id']
        ])->first();
        if ($socialLink) {
            $socialLink->delete();
            return response()->json('SERVER.SOCIAL_LINK_DELETED', 202);
        }
        return response()->json('SERVER.WRONG_SOCIAL_LINK_ID', 404);
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
            $direction = Direction::find($user['direction_id']);
            if ($direction) {
                if ($request->get('country')) {
                    $this->validate($request, ['country' => 'required|max:60',]);
                    $direction['country'] = $request->get('country');
                }
                if ($request->get('administrative_area_level_1')) {
                    $this->validate($request, ['administrative_area_level_1' => 'required|max:60',]);
                    $direction['administrative_area_level_1'] = $request->get('administrative_area_level_1');
                }
                if ($request->get('administrative_area_level_2')) {
                    $this->validate($request, ['administrative_area_level_2' => 'required|max:60',]);
                    $direction['administrative_area_level_2'] = $request->get('administrative_area_level_2');
                }
                if ($request->get('route')) {
                    $this->validate($request, ['route' => 'required|max:60',]);
                    $direction['route'] = $request->get('route');
                }
                if ($request->get('street_number')) {
                    $this->validate($request, ['street_number' => 'required',]);
                    $direction['street_number'] = $request->get('street_number');
                }
                if ($request->get('postal_code')) {
                    $this->validate($request, ['postal_code' => 'required|numeric',]);
                    $direction['postal_code'] = $request->get('postal_code');
                }
                if ($request->get('lat')) {
                    $this->validate($request, ['lat' => 'required|numeric',]);
                    $direction['lat'] = $request->get('lat');
                }
                if ($request->get('lng')) {
                    $this->validate($request, ['lng' => 'required|numeric',]);
                    $direction['lng'] = $request->get('lng');
                }
                $direction->save();
            } else {
                $direction = Direction::create([
                    'country' => $request->get('country'),
                    'administrative_area_level_1' => $request->get('administrative_area_level_1'),
                    'administrative_area_level_2' => $request->get('administrative_area_level_2'),
                    'route' => $request->get('route'),
                    'street_number' => $request->get('street_number'),
                    'postal_code' => $request->get('postal_code'),
                    'lat' => $request->get('lat'),
                    'lng' => $request->get('lng'),
                ]);
                $user['direction_id'] = $direction['id'];
                $user->save();
            }
            return response()->json($direction, 201);
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
                $this->validate($request, ['name' => 'min:4|max:60',]);
                $user['name'] = $request->get('name');
            }
            if ($request->get('first_name')) {
                $this->validate($request, ['first_name' => 'max:60',]);
                $user['first_name'] = $request->get('first_name');
            }
            if ($request->get('last_name')) {
                $this->validate($request, ['last_name' => 'max:60',]);
                $user['last_name'] = $request->get('last_name');
            }
            if ($request->get('gender')) {
                $this->validate($request, ['gender' => 'string',]);
                $user['gender'] = $request->get('gender');
            }
            if ($request->get('phone')) {
                $this->validate($request, ['phone' => 'numeric',]);
                $user['phone'] = $request->get('phone');
            }
            if ($request->get('birthday')) {
                $user['birthday'] = $request->get('birthday');
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
                    'grant_type' => 'required',
                ]);
                $validate = User::where([
                    'email' => $request->get('email'),
                    'grant_type' => $request->get('grant_type')
                ])->first();
                if ($validate) {
                    return response()->json('SERVER.USER_EMAIL_ALREADY_EXISTS', 406);
                } else {
                    $user['email'] = $request->get('email');
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
    public function updateAvatar(Request $request)
    {
        $this->validate($request, [
            'file_name' => 'required',
            'type' => 'required'
        ]);
        $file_name = $request->get('file_name');
        if ($request->get('type') == 'base64') {
            $file = base64_decode(explode(',', $request->get('file'))[1]);
        } else {
            $file = $request->file('file');
        }
        $path = $_SERVER['DOCUMENT_ROOT'] . env('APP_PUBLIC_URL', '/app') . '/img/users_avatars/';
        $fileUrl = URL::to('/') . '/img/users_avatars/' . $file_name;
        if (!File::exists($path)) {
            File::makeDirectory($path, 2777, true);
        }
        Image::make($file)->save($path . $file_name);
        $user = $request->user();
        // Evalúa si hay un archivo registrado en el servidor con el mismo nombre para eliminarlo.
        if ($user['media_id']) {
            $user['media'] = Media::find($user['media_id']);
            if (parse_url($user['media']['url'])['host'] == parse_url(URL::to('/'))['host']) {
                File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($user['media']['url'])['path']);
            }
            $user['media']['url'] = $fileUrl;
            $user['media']->save();
        } else {
            $media = Media::create([
                'url' => $fileUrl,
                'alt' => 'avatar',
            ]);
            $user['media_id'] = $media['id'];
            $user->save();
        }
        return response()->json($fileUrl, 202);
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
            $direction = Direction::find($user['direction_id']);
            if ($direction) {
                if ($request->get('country')) {
                    $this->validate($request, ['country' => 'max:60',]);
                    $direction['country'] = $request->get('country');
                }
                if ($request->get('administrative_area_level_1')) {
                    $this->validate($request, ['administrative_area_level_1' => 'max:60',]);
                    $direction['administrative_area_level_1'] = $request->get('administrative_area_level_1');
                }
                if ($request->get('administrative_area_level_2')) {
                    $this->validate($request, ['administrative_area_level_2' => 'max:60',]);
                    $direction['administrative_area_level_2'] = $request->get('administrative_area_level_2');
                }
                if ($request->get('route')) {
                    $this->validate($request, ['route' => 'max:60',]);
                    $direction['route'] = $request->get('route');
                }
                if ($request->get('street_number')) {
                    $direction['street_number'] = $request->get('street_number');
                }
                if ($request->get('postal_code')) {
                    $this->validate($request, ['postal_code' => 'numeric',]);
                    $direction['postal_code'] = $request->get('postal_code');
                }
                if ($request->get('lat')) {
                    $this->validate($request, ['lat' => 'numeric',]);
                    $direction['lat'] = $request->get('lat');
                }
                if ($request->get('lng')) {
                    $this->validate($request, ['lng' => 'numeric',]);
                    $direction['lng'] = $request->get('lng');
                }
                $direction->save();
            } else {
                $direction = Direction::create([
                    'country' => $request->get('country'),
                    'administrative_area_level_1' => $request->get('administrative_area_level_1'),
                    'administrative_area_level_2' => $request->get('administrative_area_level_2'),
                    'route' => $request->get('route'),
                    'street_number' => $request->get('street_number'),
                    'postal_code' => $request->get('postal_code'),
                    'lat' => $request->get('lat'),
                    'lng' => $request->get('lng'),
                ]);
                $user['direction_id'] = $direction['id'];
                $user->save();
            }
            return response()->json($direction, 201);
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
                $this->validate($request, ['name' => 'min:4|max:60',]);
                $user['name'] = $request->get('name');
            }
            if ($request->get('first_name')) {
                $this->validate($request, ['first_name' => 'max:60',]);
                $user['first_name'] = $request->get('first_name');
            }
            if ($request->get('last_name')) {
                $this->validate($request, ['last_name' => 'max:60',]);
                $user['last_name'] = $request->get('last_name');
            }
            if ($request->get('gender')) {
                $this->validate($request, ['gender' => 'string',]);
                $user['gender'] = $request->get('gender');
            }
            if ($request->get('phone')) {
                $this->validate($request, ['phone' => 'numeric',]);
                $user['phone'] = $request->get('phone');
            }
            if ($request->get('birthday')) {
                $user['birthday'] = $request->get('birthday');
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
                    'grant_type' => 'required',
                ]);
                $validate = User::where([
                    'email' => $request->get('email'),
                    'grant_type' => $request->get('grant_type')
                ])->first();
                if ($validate) {
                    return response()->json('SERVER.USER_EMAIL_ALREADY_EXISTS', 406);
                } else {
                    $user['email'] = $request->get('email');
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
     * Guarda un archivo en nuestro directorio local.
     * 
     * @param \Illuminate\Http\Request $request
     * @param number $id
     * @return \Illuminate\Http\Response
     */
    public function updateAvatarById(Request $request, $id)
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
        $path = $_SERVER['DOCUMENT_ROOT'] . env('APP_PUBLIC_URL', '/app') . '/img/users_avatars/';
        $fileUrl = URL::to('/') . '/img/users_avatars/' . $file_name;
        if (!File::exists($path)) {
            File::makeDirectory($path, 2775, true);
        }
        $fileMade = Image::make($file);
        $fileMade->save($path . $file_name);
        $user = User::find($id);
        // Evalúa si hay un archivo registrado en el servidor con el mismo nombre para eliminarlo.
        if ($user['media_id']) {
            $user['media'] = Media::find($user['media_id']);
            if (parse_url($user['media']['url'])['host'] == parse_url(URL::to('/'))['host']) {
                File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($user['media']['url'])['path']);
            }
            $user['media']['url'] = $fileUrl;
            $user['media']['width'] = $fileMade->width();
            $user['media']['height'] = $fileMade->height();
            $user['media']->save();
        } else {
            $media = Media::create([
                'url' => $fileUrl,
                'alt' => 'avatar',
                'width' => $fileMade->width(),
                'height' => $fileMade->height(),
            ]);
            $user['media_id'] = $media['id'];
            $user->save();
        }
        return response()->json($fileUrl, 202);
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
        return response()->json('SERVER.LOGGEDOUT', 202);
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
            if ($user['media_id']) {
                $user['media'] = Media::find($user['media_id']);
                if (parse_url($user['media']['url'])['host'] == parse_url(URL::to('/'))['host']) {
                    File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($user['media']['url'])['path']);
                }
            }    
            $user->delete();
            return response()->json('SERVER.USER_DELETED', 202);
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
        if ($user['direction_id']) {
            Direction::find($user['direction_id'])->delete();
        }
        if ($user['media_id']) {
            $media = Media::find($user['media_id']);
            if (parse_url($media['url'])['host'] == parse_url(URL::to('/'))['host']) {
                File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($media['url'])['path']);
            }
            $media->delete();
        }
        $user->delete();
        return response()->json('SERVER.USER_DELETED', 200);
    }
}
