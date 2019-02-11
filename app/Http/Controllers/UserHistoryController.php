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

class UserHistoryController extends BaseController
{
    /**
     * Recupera el historial de un usuario.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id) {
        return response()->json(
            $this->attachData(Pet::where('user_id', $id)->orderBy('updated_at', 'DESC')->paginate(5))
        , 200);
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
