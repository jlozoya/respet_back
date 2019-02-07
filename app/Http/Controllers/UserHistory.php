<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;
use App\Models\EmailConfirm;
use App\Models\Direction;
use App\Models\Media;
use App\Models\UserPermissions;
use App\Models\CatEmail;
use App\Models\CatPhone;
use App\Models\PetMedia;
use App\Models\Pet;

use Illuminate\Http\Request;

class UserHistory extends BaseController
{
    /**
     * Recupera el historial de un usuario.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return App\Models\User $user
     */
    public function index(Request $request, $id) {
        $user = User::find($id);
        $permissions = UserPermissions::find($user['permissions_id']);
        $contact;
        if ($permissions['show_main_email']) {
            $contact['email'] = $user['email'];
        }
        if ($permissions['show_alternative_emails']) {
            $contact['emails'] = CatEmail::where('user_id', $id)->get();
        }
        if ($permissions['show_main_phone']) {
            $contact['phone'] = $user['phone'];
        }
        if ($permissions['show_alternative_phones']) {
            $contact['phones'] = CatPhone::where('user_id', $id)->get();
        }
        if ($permissions['show_direction']) {
            if ($user['direction_id']) {
                $contact['direction'] = Direction::find($user['direction_id']);
            }
        }
        return response()->json([
            'id' => $user['id'],
            'name' => $user['name'],
            'contact' => $contact,
            'history' => $this->attachData(Pet::where('user_id', $id)->orderBy('updated_at', 'DESC')->paginate(5))
        ], 200);
    }
    /**
     * Agrega información a la consulta.
     * 
     * @param $pets
     * @return \Illuminate\Http\Response
     */
    private function attachData($pets) {
        foreach ($pets as &$pet) {
            $pet['user'] = User::select(
                'id',
                'name',
                'media_id'
            )->where('id', $pet['user_id'])->first();
            if ($pet['user']['media_id']) {
                $pet['user']['media'] = Media::find($pet['user']['media_id']);
            }
            if ($pet['direction_id']) {
                $pet['direction'] = Direction::find($pet['direction_id']);
            }
            $pet['media'] = PetMedia::select('media.*')->where('pet_media.pet_id', $pet['id'])
            ->join('media', 'pet_media.media_id', 'media.id')->get();
        }
        return $pets;
    }
}
