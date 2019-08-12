<?php
namespace App\Http\Controllers\Store;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\Store\Warehouse;
use App\Models\Store\Product;
use App\Models\Generic\Direction;
use App\Models\Generic\Media;
use DB;

use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;

use Carbon\Carbon;

class WarehouseController extends BaseController {
    /**
     * Muestra una lista del recurso.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->get('search')) {
            $warehouses = Warehouse::where('name', 'like', '%' . $request->get('search') . '%')
            ->orderBy('updated_at', 'DESC')->paginate(6);
            return $this->attachData($warehouses);
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
            return $this->attachData(Warehouse::select('warehouses.*')
            ->join('directions', 'warehouses.direction_id', '=', 'directions.id')
            ->whereRaw($where)
            ->orderBy('updated_at', 'DESC')
            ->paginate(6));
        } else if ($request->get('latLng')) {
            $latlng = explode(',', $request->get('latLng'));
            $distance = 10;
            $directions = DB::table('directions')
            ->select(DB::raw("`id`, (acos(sin(radians($latlng[0])) * sin(radians(`lat`)) + 
            cos(radians($latlng[0])) * cos(radians(`lat`)) * 
            cos(radians($latlng[1]) - radians(`lng`))) * 6378) as 
            `distance`"))
            ->havingRaw("distance <= $distance")
            ->where('lat', '!=', 0)
            ->where('lng', '!=', 0)
            ->get()->toArray();
            $warehouses = [];
            foreach ($directions as &$direction) {
                $warehouse = Warehouse::where('direction_id', $direction->id)->first();
                if ($warehouse) {
                    array_push($warehouses, $warehouse);
                }
            }
            return $this->attachData($warehouses);
        } else {
            return $this->attachData(Warehouse::orderBy('updated_at', 'DESC')->paginate(6));
        }
    }

    /**
     * Agrega información a la consulta.
     * 
     * @param $warehouses
     * @return \Illuminate\Http\Response
     */
    private function attachData($warehouses) {
        foreach ($warehouses as &$warehouse) {
            if ($warehouse['direction_id']) {
                $warehouse['direction'] = Direction::find($warehouse['direction_id']);
            }
            if ($warehouse['media_id']) {
                $warehouse['media'] = Media::find($warehouse['media_id']);
            }
        }
        return response()->json($warehouses, 200);
    }

    /**
     * Almacenar un recurso recién creado en el almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $warehouse = Warehouse::create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'direction_id' => $request->get('direction_id'),
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
            $warehouse['direction_id'] = $direction['id'];
            $warehouse->save();
            $warehouse['direction'] = $direction;
        }
        return response()->json($warehouse, 201);
    }

    /**
     * Almacenar un recurso recién creado en el almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFile(Request $request, $id) {
        $this->validate($request, [
            'file_name' => 'required',
            'type' => 'required',
        ]);
        $fileName = $request->get('file_name');
        if ($request->get('type') == 'base64') {
            $file = base64_decode(explode(',', $request->get('file'))[1]);
        } else {
            $file = $request->file('file');
        }
        $date = Carbon::now()->toDateString();
        $path = $_SERVER['DOCUMENT_ROOT'] . env('APP_PUBLIC_URL', '/app') . '/img/warehouses/' . $date . '/';
        $fileUrl = URL::to('/') . '/img/warehouses/' . $date . '/' . $fileName;
        if (!File::exists($path)) {
            File::makeDirectory($path, 2777, true);
        }
        $image = Image::make($file)->save($path . $fileName);
        $data = getimagesize($path . $fileName);
        $warehouse = Warehouse::find($id);
        $media;
        if ($warehouse['media_id']) {
            $media = Media::find($warehouse['media_id']);
            if (parse_url($media['url'])['host'] == parse_url(URL::to('/'))['host']) {
                File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($media['url'])['path']);
            }
            $media['url'] = $fileUrl;
            $media['width'] = $data[0];
            $media['height'] = $data[1];
            $media->save();
        } else {
            $media = Media::create([
                'type' => $request->input('params.type') || 'img',
                'url' => $fileUrl,
                'alt' => $fileName,
                'width' => $data[0],
                'height' => $data[1],
            ]);
            $warehouse['media_id'] = $media['id'];
            $warehouse->save();
        }
        return response()->json($media, 202);
    }

    /**
     * Mostrar el recurso especificado.
     *
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $warehouse = Warehouse::find($id);
        if ($warehouse['direction_id']) {
            $warehouse['direction'] = Direction::find($warehouse['direction_id']);
        }
        if ($warehouse['media_id']) {
            $warehouse['media'] = Media::find($warehouse['media_id']);
        }
        return response()->json($warehouse, 200);
    }

    /**
     * Actualizar el recurso especificado en el almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $warehouse = Warehouse::find($id);
        if ($warehouse) {
            if ($request->get('name')) {
                $this->validate($request, ['name' => 'string',]);
                $warehouse['name'] = $request->get('name');
            }
            if ($request->get('description')) {
                $this->validate($request, ['description' => 'string',]);
                $warehouse['description'] = $request->get('description');
            }
            $direction;
            if ($warehouse['direction_id']) {
                $this->validate($request, ['direction_id' => 'numeric',]);
                if ($request->get('direction')) {
                    $direction = Direction::find($warehouse['direction_id']);
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
                    $warehouse['direction_id'] = $direction['id'];
                }
            }
            $warehouse->save();
            if ($direction) {
                $warehouse['direction'] = $direction;
            }
            if ($warehouse['media_id']) {
                $warehouse['media'] = Media::find($warehouse['media_id']);
            }
            return response()->json($warehouse, 201);
        }
        return response()->json('SERVER.WAREHOUSE_NOT_FOUND', 404);
    }
    /**
     * Eliminar el recurso especificado del almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
        if (!Product::where('warehouse_id', $id)->count()) {
            $warehouse = Warehouse::find($id);
            if ($warehouse['direction_id']) {
                Direction::find($warehouse['direction_id'])->delete();
            }
            if ($warehouse['media_id']) {
                $media = Media::find($warehouse['media_id']);
                if (parse_url($media['url'])['host'] == parse_url(URL::to('/'))['host']) {
                    File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($media['url'])['path']);
                }
                $media->delete();
            }
            $warehouse->delete();
            return response()->json(null, 204);
        }
        return response()->json('SERVER.WAREHOUSE_NOT_EMPTY', 406);
    }
}
