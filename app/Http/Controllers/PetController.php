<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Direction;
use App\Models\Media;

use Illuminate\Http\Request;

class PetController extends Controller
{
    /**
     * Muestra una lista del recurso.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $pets = Pet::all()->paginate(5);
        foreach ($pets as &$pet) {
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
            $direction = Media::create([
                'contry' => $request->get('direction')['contry'],
                'administrative_area_level_1' => $request->get('direction')['administrative_area_level_1'],
                'administrative_area_level_2' => $request->get('direction')['administrative_area_level_2'],
                'route' => $request->get('direction')['route'],
                'street_number' => $request->get('direction')['street_number'],
                'postal_code' => $request->get('direction')['postal_code'],
                'lat' => $request->get('direction')['lat'],
                'lng' => $request->get('direction')['lng'],
            ]);
            $pet['direction_id'] = $direction['id'];
            $pet->save();
        }
        if ($request->get('media')) {
            $media = Media::create([
                'url' => $request->get('media')['url'],
                'alt' => $request->get('media')['alt'],
                'width' => $request->get('media')['width'],
                'height' => $request->get('media')['height'],
            ]);
            $pet['media_id'] = $media['id'];
            $pet->save();
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
                        'country' => $request->get('direction')['country'],
                        'administrative_area_level_1' => $request->get('direction')['administrative_area_level_1'],
                        'administrative_area_level_2' => $request->get('direction')['administrative_area_level_2'],
                        'route' => $request->get('direction')['route'],
                        'street_number' => $request->get('direction')['street_number'],
                        'postal_code' => $request->get('direction')['postal_code'],
                        'lat' => $request->get('direction')['lat'],
                        'lng' => $request->get('direction')['lng'],
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
