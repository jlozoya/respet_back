<?php

namespace App\Http\Controllers\User;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User\User;
use App\Models\User\EmailConfirm;
use App\Models\Generic\Address;
use App\Models\Generic\Media;
use App\Models\User\SocialLink;
use App\Models\User\UserPermissions;

use App\Traits\PassportToken;

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
    use PassportToken;
    
    /**
     * Recupera la información básica de un usuario.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $user = $request->user();
        if ($user) {
            if ($user['address_id']) {
                $user['address'] = Address::find($user['address_id']);
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
            'address_id'
        )->find($id);
        if ($user) {
            if ($user['address_id']) {
                $user['address'] = Address::find($user['address_id']);
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
        )->where('role', 'like', '%' . $request->get('role') . '%')
        ->where(function ($query) use ($request) {
            $search = $request->get('search');
            $query->where('name', 'like', "%$search%");
            $query->orWhere('email', 'like', "%$search%");
            $query->orWhere('phone', 'like', "%$search%");
        })->orderBy('updated_at', 'DESC')
        ->paginate(15);
        foreach ($users as &$user) {
            if ($user['address_id']) {
                $user['address'] = Address::find($user['address_id']);
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
        $client = $this->checkClient($request);
        if ($client) {
            if ($request->get('grant_type') == 'password') {
                $user = User::where([
                    'grant_type' => $request->get('grant_type'), 'email' => $request->get('email')
                ])->first();
                if ($user && $user['confirmed']) {
                    return response()->json('SERVER.USER_ALREADY_EXISTS', 401);
                }
                if ($user && !$user['confirmed']) {
                    $emailConfirm = EmailConfirm::where('user_id', $user['id'])->first();
                    $dateTimeNow = Carbon::now();
                    if ($emailConfirm) {
                        $dateTimeCreatedAt = Carbon::parse($emailConfirm['created_at']);
                        if ($dateTimeNow->diffInDays($dateTimeCreatedAt) <= 5) {
                            return response()->json('SERVER.USER_ALREADY_EXISTS', 401);
                        }
                    }
                    $this->deleteUserById($user['id']);
                }
                $user = $this->passwordStore($request);
            } else {
                $this->validate($request, [
                    'extern_id' => 'required',
                ]);
                $socialLink = SocialLink::where([
                    'grant_type' => $request->get('grant_type'),
                    'extern_id' => $request->get('extern_id')
                ])->first();
                if ($socialLink) {
                    return response()->json('SERVER.USER_ALREADY_EXISTS', 401);
                }
                $user = $this->notPasswordStore($request);
            }
            $user['permissions_id'] = UserPermissions::create()['id'];
            $user->save();
            if ($request->get('media')) {
                $media = Media::create([
                    'url' => $request->input('media.url'),
                    'alt' => $request->input('media.alt')? $request->input('media.alt') : 'media',
                    'width' => $request->input('media.width'),
                    'height' => $request->input('media.height'),
                ]);
                $user['media_id'] = $media['id'];
                $user->save();
            }
            $sesion = $this->getBearerTokenByUser($user, $client['id'], false);
            $this->sendConfirmEmail($user);
            return response()->json($sesion, 201);
        } else {
            return response()->json("SERVER.WRONG_CLIENT", 401);
        }
    }
    /**
     * Guarda un usuario con contraseña.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return App\Models\User\User $user
     */
    private function passwordStore(Request $request) {
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
        if ($request->get('address')) {
            $address = Address::create([
                'country' => $request->input('address.country'),
                'administrative_area_level_1' => $request->input('address.administrative_area_level_1'),
                'administrative_area_level_2' => $request->input('address.administrative_area_level_2'),
                'route' => $request->input('address.route'),
                'street_number' => $request->input('address.street_number'),
                'postal_code' => $request->input('address.postal_code'),
                'lat' => $request->input('address.lat'),
                'lng' => $request->input('address.lng'),
            ]);
            $user['address_id'] = $address['id'];
            $user->save();
            $user['address'] = $address;
        }
        return $user;
    }
    /**
     * Guarda un usuario sin contraseña.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return App\Models\User\User $user
     */
    private function notPasswordStore(Request $request) {
        $user = User::create([
            'name' => $request->get('name'),
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'gender' => $request->get('gender'),
            'email' => $request->get('email'),
            'lang' => $request->get('lang'),
            'grant_type' => $request->get('grant_type'),
        ]);
        SocialLink::create([
            'user_id' => $user['id'],
            'grant_type' => $request->get('grant_type'),
            'extern_id' => $request->get('extern_id'),
        ]);
        return $user;
    }
    /**
     * Llama la función para enviar un correo de confirmación.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function reSendConfirmEmail(Request $request) {
        $user = $request->user();
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
    public function sendConfirmEmail($user, $json = true) {
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
        if ($json) {
            if ($response == '') {
                return response()->json('SERVER.EMAIL_SEND', 200);
            } else {
                return response()->json('SERVER.EMAIL_FAIL', 400);
            }
        } else {
            return $response;
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
            if ($dateTimeNow->diffInDays($dateTimeCreatedAt) <= 5) {
                if (!User::where('id' , '!=', $emailConfirmData['user_id'])->where('email', $emailConfirmData['email'])->first()) {
                    $user = User::find($emailConfirmData['user_id']);
                    $user['confirmed'] = true;
                    $user['email'] = $emailConfirmData['email'];
                    $user->save();
                    $emailConfirmData->delete();
                    return redirect(env('APP_REDIRECTS_LINK', '../'));
                }
                $emailConfirmData->delete();
                return response()->json('SERVER.EMAIL_HAS_TAKEN', 406);
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
            } break;
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
            } break;
            default: {
                return response()->json('SERVER.GRANT_TYPE_NOT_FOUND', 404);
            } break;
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
            return response()->json(null, 204);
        }
        return response()->json('SERVER.WRONG_SOCIAL_LINK_ID', 404);
    }
    /**
     * Actualizar la dirección del usuario.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateUserAddress(Request $request) {
        try {
            $user = $request->user();
            $address = Address::find($user['address_id']);
            if ($address) {
                if ($request->get('country')) {
                    $this->validate($request, ['country' => 'required|max:60',]);
                    $address['country'] = $request->get('country');
                }
                if ($request->get('administrative_area_level_1')) {
                    $this->validate($request, ['administrative_area_level_1' => 'required|max:60',]);
                    $address['administrative_area_level_1'] = $request->get('administrative_area_level_1');
                }
                if ($request->get('administrative_area_level_2')) {
                    $this->validate($request, ['administrative_area_level_2' => 'required|max:60',]);
                    $address['administrative_area_level_2'] = $request->get('administrative_area_level_2');
                }
                if ($request->get('route')) {
                    $this->validate($request, ['route' => 'required|max:60',]);
                    $address['route'] = $request->get('route');
                }
                if ($request->get('street_number')) {
                    $this->validate($request, ['street_number' => 'required',]);
                    $address['street_number'] = $request->get('street_number');
                }
                if ($request->get('postal_code')) {
                    $this->validate($request, ['postal_code' => 'required|numeric',]);
                    $address['postal_code'] = $request->get('postal_code');
                }
                if ($request->get('lat')) {
                    $this->validate($request, ['lat' => 'required|numeric',]);
                    $address['lat'] = $request->get('lat');
                }
                if ($request->get('lng')) {
                    $this->validate($request, ['lng' => 'required|numeric',]);
                    $address['lng'] = $request->get('lng');
                }
                $address->save();
            } else {
                $address = Address::create([
                    'country' => $request->get('country'),
                    'administrative_area_level_1' => $request->get('administrative_area_level_1'),
                    'administrative_area_level_2' => $request->get('administrative_area_level_2'),
                    'route' => $request->get('route'),
                    'street_number' => $request->get('street_number'),
                    'postal_code' => $request->get('postal_code'),
                    'lat' => $request->get('lat'),
                    'lng' => $request->get('lng'),
                ]);
                $user['address_id'] = $address['id'];
                $user->save();
            }
            return response()->json($address, 201);
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
        $this->validate($request, [
            'email' => 'required|email',
            'grant_type' => 'required',
        ]);
        $user = $request->user();
        if ($user) {
            if (!$validate = User::where([
                'email' => $request->get('email'),
                'grant_type' => $request->get('grant_type')
            ])->first()) {
                $user['email'] = $request->get('email');
                $user['confirmed'] = false;
                $user->save();
                $this->sendConfirmEmail($user, false);
                return response()->json($user, 201);
            } else {
                return response()->json('SERVER.USER_EMAIL_ALREADY_EXISTS', 406);
            }
        }
        return response()->json('SERVER.USER_NOT_FOUND', 404);
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
        $fileName = $request->get('file_name');
        if ($request->get('type') == 'base64') {
            $file = base64_decode(explode(',', $request->get('file'))[1]);
        } else {
            $file = $request->file('file');
        }
        $date = Carbon::now()->toDateString();
        $path = $_SERVER['DOCUMENT_ROOT'] . env('APP_PUBLIC_URL', '/app') . '/img/users_avatars/' . $date . '/';
        $fileUrl = URL::to('/') . '/img/users_avatars/' . $date . '/' . $fileName;
        if (!File::exists($path)) {
            File::makeDirectory($path, 2777, true);
        }
        Image::make($file)->save($path . $fileName);
        $data = getimagesize($path . $fileName);
        $user = $request->user();
        // Evalúa si hay un archivo registrado en el servidor con el mismo nombre para eliminarlo.
        if ($user['media_id']) {
            $user['media'] = Media::find($user['media_id']);
            if (parse_url($user['media']['url'])['host'] == parse_url(URL::to('/'))['host']) {
                File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($user['media']['url'])['path']);
            }
            $user['media']['url'] = $fileUrl;
            $user['media']['width'] = $data[0];
            $user['media']['height'] = $data[1];
            $user['media']->save();
        } else {
            $media = Media::create([
                'url' => $fileUrl,
                'alt' => $fileName,
                'width' => $data[0],
                'height' => $data[1],
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
    public function updateUserAddressById(Request $request, $id) {
        try {
            $user = User::find($id);
            $address = Address::find($user['address_id']);
            if ($address) {
                if ($request->get('country')) {
                    $this->validate($request, ['country' => 'max:60',]);
                    $address['country'] = $request->get('country');
                }
                if ($request->get('administrative_area_level_1')) {
                    $this->validate($request, ['administrative_area_level_1' => 'max:60',]);
                    $address['administrative_area_level_1'] = $request->get('administrative_area_level_1');
                }
                if ($request->get('administrative_area_level_2')) {
                    $this->validate($request, ['administrative_area_level_2' => 'max:60',]);
                    $address['administrative_area_level_2'] = $request->get('administrative_area_level_2');
                }
                if ($request->get('route')) {
                    $this->validate($request, ['route' => 'max:60',]);
                    $address['route'] = $request->get('route');
                }
                if ($request->get('street_number')) {
                    $address['street_number'] = $request->get('street_number');
                }
                if ($request->get('postal_code')) {
                    $this->validate($request, ['postal_code' => 'numeric',]);
                    $address['postal_code'] = $request->get('postal_code');
                }
                if ($request->get('lat')) {
                    $this->validate($request, ['lat' => 'numeric',]);
                    $address['lat'] = $request->get('lat');
                }
                if ($request->get('lng')) {
                    $this->validate($request, ['lng' => 'numeric',]);
                    $address['lng'] = $request->get('lng');
                }
                $address->save();
            } else {
                $address = Address::create([
                    'country' => $request->get('country'),
                    'administrative_area_level_1' => $request->get('administrative_area_level_1'),
                    'administrative_area_level_2' => $request->get('administrative_area_level_2'),
                    'route' => $request->get('route'),
                    'street_number' => $request->get('street_number'),
                    'postal_code' => $request->get('postal_code'),
                    'lat' => $request->get('lat'),
                    'lng' => $request->get('lng'),
                ]);
                $user['address_id'] = $address['id'];
                $user->save();
            }
            return response()->json($address, 201);
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
        $this->validate($request, [
            'email' => 'required|email',
            'grant_type' => 'required',
        ]);
        $user = User::find($id);
        if ($user) {
            if (!User::where([
                'email' => $request->get('email'),
                'grant_type' => $request->get('grant_type')
            ])->first()) {
                $user['email'] = $request->get('email');
                $this->sendConfirmEmail($user);
                return response()->json($user, 201);
            } else {
                return response()->json('SERVER.USER_EMAIL_ALREADY_EXISTS', 406);
            }
        }
        return response()->json('SERVER.USER_NOT_FOUND', 404);
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
    public function updateAvatarById(Request $request, $id) {
        $this->validate($request, [
            'file_name' => 'required',
            'type' => 'required'
        ]);
        $fileName = $request->get('file_name');
        if ($request['type'] == 'base64') {
            $file = base64_decode(explode(',', $request['file'])[1]);
        } else {
            $file = $request->file('file');
        }
        $date = Carbon::now()->toDateString();
        $path = $_SERVER['DOCUMENT_ROOT'] . env('APP_PUBLIC_URL', '/app') . '/img/users_avatars/' . $date . '/';
        $fileUrl = URL::to('/') . '/img/users_avatars/' . $date . '/' . $fileName;
        if (!File::exists($path)) {
            File::makeDirectory($path, 2775, true);
        }
        $fileMade = Image::make($file)->save($path . $fileName);
        $data = getimagesize($path . $fileName);
        $user = User::find($id);
        // Evalúa si hay un archivo registrado en el servidor con el mismo nombre para eliminarlo.
        if ($user['media_id']) {
            $user['media'] = Media::find($user['media_id']);
            if (parse_url($user['media']['url'])['host'] == parse_url(URL::to('/'))['host']) {
                File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($user['media']['url'])['path']);
            }
            $user['media']['type'] = $request->input('params.type') || 'img';
            $user['media']['url'] = $fileUrl;
            $user['media']['width'] = $data[0];
            $user['media']['height'] = $data[1];
            $user['media']->save();
        } else {
            $media = Media::create([
                'type' => $request->input('params.type') || 'img',
                'url' => $fileUrl,
                'alt' => $fileName,
                'width' => $data[0],
                'height' => $data[1],
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
        return response()->json(null, 204);
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
            if ($user['address_id']) {
                Address::find($user['address_id'])->delete();
            }
            if ($user['media_id']) {
                $user['media'] = Media::find($user['media_id']);
                if (parse_url($user['media']['url'])['host'] == parse_url(URL::to('/'))['host']) {
                    File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($user['media']['url'])['path']);
                }
            }
            if ($user['permissions_id']) {
                UserPermissions::find($user['permissions_id'])->delete();
            }
            $user->delete();
            return response()->json(null, 204);
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
        if ($user) {
            if ($user['address_id']) {
                Address::find($user['address_id'])->delete();
            }
            if ($user['media_id']) {
                $media = Media::find($user['media_id']);
                if (parse_url($media['url'])['host'] == parse_url(URL::to('/'))['host']) {
                    File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($media['url'])['path']);
                }
                $media->delete();
            }
            if ($user['permissions_id']) {
                UserPermissions::find($user['permissions_id'])->delete();
            }
            $user->delete();
            return response()->json(null, 204);
        }
        return response()->json('SERVER.USER_NOT_FOUND', 404);
    }
}
