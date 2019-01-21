<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\Pet;
use App\Models\Direction;
use App\Models\User;
use App\Models\Media;

use Illuminate\Http\Request;

class PetController extends BaseController
{
    /**
     * Muestra una lista del recurso.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->get('search')) {
            $pets = Pet::where('name', 'like', '%' . $request->get('search') . '%')
            ->orWhere('description', 'like', '%' . $request->get('search') . '%')
            ->orWhere('state', 'like', '%' . $request->get('search') . '%')
            ->paginate(5);
            return $this->attachData($pets);
        } else if ($request->get('direction')) {
            $where = "";
            if ($request->input('direction.country')) {
                $where .= " `directions`.`country` LIKE '%" . $request->input('direction.country') . "%'";
            }
            if ($request->input('direction.administrative_area_level_1')) {
                $where .= " `directions`.`administrative_area_level_1` LIKE '%" . $request->input('direction.administrative_area_level_1') . "%'";
            }
            if ($request->input('direction.administrative_area_level_2')) {
                $where .= " `directions`.`administrative_area_level_2` LIKE '%" . $request->input('direction.administrative_area_level_2') . "%'";
            }
            if ($request->input('direction.route')) {
                $where .= " `directions`.`route` LIKE '%" . $request->input('direction.route') . "%'";
            }
            if ($request->input('direction.street_number')) {
                $where .= " `directions`.`street_number` LIKE '%" . $request->input('direction.street_number') . "%'";
            }
            if ($request->input('direction.postal_code')) {
                $where .= " `directions`.`postal_code` LIKE '%" . $request->input('direction.postal_code') . "%'";
            }
            if ($request->input('direction.lat')) {
                $where .= " `directions`.`lat` LIKE '%" . $request->input('direction.lat') . "%'";
            }
            if ($request->input('direction.lng')) {
                $where .= " `directions`.`lng` LIKE '%" . $request->input('direction.lng') . "%'";
            }
            return $this->attachData(Pet::select('pets.*')
            ->join('directions', 'pets.direction_id', '=', 'directions.id')
            ->whereRaw($where)
            ->paginate(5));
        } else {
            return $this->attachData(Pet::paginate(5));
        }
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
            if ($pet['media_id']) {
                $pet['media'] = Media::find($pet['media_id']);
            }
        }
        return response()->json($pets, 200);
    }

    /**
     * Almacenar un recurso recién creado en el almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $user = $request->user();
        $this->validate($request, [
            'name' => 'required|max:60',
        ]);
        $pet = Pet::create([
            'user_id' => $user['id'],
            'name' => $request->get('name'),
            'show_contact_information' => $request->get('show_contact_information'),
            'description' => $request->get('description'),
            'state' => $request->get('state'),
            'direction_id' => $request->get('direction_id'),
            'direction_accuracy' => $request->get('direction_accuracy'),
            'media_id' => $request->get('media_id'),
        ]);
        if ($request->get('direction')) {
            $direction = Direction::create([
                'country' => $request->input('direction.country'),
                'administrative_area_level_1' => $request->input('direction.administrative_area_level_1'),
                'administrative_area_level_2' => $request->input('direction.administrative_area_level_2'),
                'route' => $request->input('direction.route'),
                'street_number' => $request->input('direction.street_number'),
                'postal_code' => $request->input('direction.postal_code'),
                'lat' => $request->input('direction.lat'),
                'lng' => $request->input('direction.lng'),
            ]);
            $pet['direction_id'] = $direction['id'];
            $pet->save();
            $pet['direction'] = $direction;
        }
        if ($request->get('media')) {
            $media = Media::create([
                'url' => $request->input('media.url'),
                'alt' => $request->input('media.alt'),
                'width' => $request->input('media.width'),
                'height' => $request->input('media.height'),
            ]);
            $pet['media_id'] = $media['id'];
            $pet->save();
            $pet['media'] = $media;
        }
        return response()->json($pet, 201);
    }

    /**
     * Mostrar el recurso especificado.
     *
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $pet = Pet::find($id);
        if ($pet['direction_id']) {
            $pet['direction'] = Direction::find($pet['direction_id']);
        }
        if ($pet['media_id']) {
            $pet['media'] = Media::find($pet['media_id']);
        }
        return response()->json($pet, 200);
    }

    /**
     * Actualizar el recurso especificado en el almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        try {
            $pet = Pet::find($id);
            if ($request->get('user_id')) {
                $this->validate($request, ['user_id' => 'number',]);
                $pet['user_id'] = $request->get('user_id');
            }
            if ($request->get('name')) {
                $this->validate($request, ['name' => 'max:60',]);
                $pet['name'] = $request->get('name');
            }
            if ($request->get('show_contact_information')) {
                $this->validate($request, ['show_contact_information' => 'max:60',]);
                $pet['show_contact_information'] = $request->get('show_contact_information');
            }
            if ($request->get('description')) {
                $this->validate($request, ['description' => 'string',]);
                $pet['description'] = $request->get('description');
            }
            // 'found' | 'lost' | 'on_adoption' | 'on_sale' | 'on_hold' | 'other'
            if ($request->get('state')) {
                $this->validate($request, ['state' => 'string',]);
                $pet['state'] = $request->get('state');
            }
            $direction;
            if ($request->get('direction_id')) {
                $this->validate($request, ['direction_id' => 'numeric',]);
                $pet['direction_id'] = $request->get('direction_id');
                if ($request->get('direction')) {
                    $direction = Direction::find($pet['direction_id']);
                    if ($request->get('direction')['country']) {
                        $this->validate($request, ['direction.country' => 'max:60',]);
                        $direction['country'] = $request->get('direction')['country'];
                    }
                    if ($request->get('direction')['administrative_area_level_1']) {
                        $this->validate($request, ['direction.administrative_area_level_1' => 'max:60',]);
                        $direction['administrative_area_level_1'] = $request->get('direction')['administrative_area_level_1'];
                    }
                    if ($request->get('direction')['administrative_area_level_2']) {
                        $this->validate($request, ['direction.administrative_area_level_2' => 'max:60',]);
                        $direction['administrative_area_level_2'] = $request->get('direction')['administrative_area_level_2'];
                    }
                    if ($request->get('direction')['route']) {
                        $this->validate($request, ['direction.route' => 'max:60',]);
                        $direction['route'] = $request->get('direction')['route'];
                    }
                    if ($request->get('direction')['street_number']) {
                        $direction['street_number'] = $request->get('direction')['street_number'];
                    }
                    if ($request->get('direction')['postal_code']) {
                        $this->validate($request, ['direction.postal_code' => 'numeric',]);
                        $direction['postal_code'] = $request->get('direction')['postal_code'];
                    }
                    if ($request->get('direction')['lat']) {
                        $this->validate($request, ['direction.lat' => 'numeric',]);
                        $direction['lat'] = $request->get('direction')['lat'];
                    }
                    if ($request->get('direction')['lng']) {
                        $this->validate($request, ['direction.lng' => 'numeric',]);
                        $direction['lng'] = $request->get('direction')['lng'];
                    }
                    $direction->save();
                }
            } else {
                if ($request->get('direction')) {
                    $direction = Direction::create([
                        'country' => $request->input('direction.country'),
                        'administrative_area_level_1' => $request->input('direction.administrative_area_level_1'),
                        'administrative_area_level_2' => $request->input('direction.administrative_area_level_2'),
                        'route' => $request->input('direction.route'),
                        'street_number' => $request->input('direction.street_number'),
                        'postal_code' => $request->input('direction.postal_code'),
                        'lat' => $request->input('direction.lat'),
                        'lng' => $request->input('direction.lng'),
                    ]);
                    $pet['direction_id'] = $direction['id'];
                }
            }
            $pet->save();
            if ($direction) {
                $pet['direction'] = $direction;
            }
            if ($pet['media_id']) {
                $pet['media'] = Media::find($pet['media_id']);
            }
            return response()->json($pet, 201);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }

    /**
     * Eliminar el recurso especificado del almacenamiento.
     *
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $pet = Pet::find($id);
        if($pet['direction_id']) {
            Direction::find($pet['direction_id'])->delete();
        }
        if ($pet['media_id']) {
            $media = Media::find($pet['media_id']);
            if (parse_url($media['url'])['host'] == parse_url(URL::to('/'))['host']) {
                File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($media['url'])['path']);
            }
            $media->delete();
        }
        $pet->delete();
        return response()->json('SERVER.PET_DELETED', 200);
    }
}
