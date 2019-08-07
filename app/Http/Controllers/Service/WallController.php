<?php

namespace App\Http\Controllers\Service;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\Service\Post;
use App\Models\Generic\Direction;
use App\Models\User\User;
use App\Models\Service\PostMedia;
use App\Models\Generic\Media;
use DB;

use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;

use Carbon\Carbon;

class WallController extends BaseController
{
    /**
     * Muestra una lista del recurso.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->get('search')) {
            if ($request->get('user_id')) {
                $posts = Post::where('user_id', $request->get('user_id'))
                ->where(function ($query) use ($request) {
                    $search = $request->get('search');
                    $query->where('state', 'like', "%$search%");
                    $query->orWhere('description', 'like', "%$search%");
                })->orderBy('updated_at', 'DESC')
                ->paginate(5);
                return $this->attachData($posts);
            }
            $posts = Post::where('description', 'like', '%' . $request->get('search') . '%')
            ->orWhere('state', 'like', '%' . $request->get('search') . '%')
            ->orderBy('updated_at', 'DESC')
            ->paginate(5);
            return $this->attachData($posts);
        } else if ($request->get('user_id')) {
            return $this->attachData(Post::where('user_id', $request->get('user_id'))->orderBy('updated_at', 'DESC')->paginate(5));
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
            return $this->attachData(Post::select('posts.*')
            ->join('directions', 'posts.direction_id', '=', 'directions.id')
            ->whereRaw($where)
            ->orderBy('updated_at', 'DESC')
            ->paginate(5));
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
            ->get()->toArray();
            $posts = [];
            foreach ($directions as $direction) {
                $post = Post::where('direction_id', $direction->id)->first();
                if ($post) {
                    array_push($posts, $post);
                }
            }
            return $this->attachData($posts);
        } else {
            return $this->attachData(Post::orderBy('updated_at', 'DESC')->paginate(5));
        }
    }

    /**
     * Agrega información a la consulta.
     * 
     * @param $posts
     * @return \Illuminate\Http\Response
     */
    private function attachData($posts) {
        foreach ($posts as &$post) {
            $post['user'] = User::select(
                'id',
                'name',
                'media_id'
            )->where('id', $post['user_id'])->first();
            if ($post['user']['media_id']) {
                $post['user']['media'] = Media::find($post['user']['media_id']);
            }
            if ($post['direction_id']) {
                $post['direction'] = Direction::find($post['direction_id']);
            }
            $post['media'] = PostMedia::select('media.*')->where('post_media.post_id', $post['id'])
            ->join('media', 'post_media.media_id', 'media.id')->get();
        }
        return response()->json($posts, 200);
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
            'description' => 'required',
            'state' => 'required',
        ]);
        $post = Post::create([
            'user_id' => $user['id'],
            'description' => $request->get('description'),
            'state' => $request->get('state'),
            'direction_id' => $request->get('direction_id'),
            'direction_accuracy' => $request->get('direction_accuracy'),
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
            $post['direction_id'] = $direction['id'];
            $post->save();
            $post['direction'] = $direction;
        }
        $post['user'] = $user;
        if ($post['user']['media_id']) {
            $post['user']['media'] = Media::find($post['user']['media_id']);
        }
        return response()->json($post, 201);
    }

    /**
     * Almacenar un recurso recién creado en el almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFile(Request $request) {
        $this->validate($request, [
            'file_name' => 'required',
            'type' => 'required',
            'params.id' => 'required'
        ]);
        $fileName = $request->get('file_name');
        if ($request->get('type') == 'base64') {
            $file = base64_decode(explode(',', $request->get('file'))[1]);
        } else {
            $file = $request->file('file');
        }
        $date = Carbon::now()->toDateString();
        $path = $_SERVER['DOCUMENT_ROOT'] . env('APP_PUBLIC_URL', '/app') . '/img/posts/' . $date . '/';
        $fileUrl = URL::to('/') . '/img/posts/' . $date . '/' . $fileName;
        if (!File::exists($path)) {
            File::makeDirectory($path, 2777, true);
        }
        $image = Image::make($file)->save($path . $fileName);
        $data = getimagesize($path . $fileName);
        $media = Media::create([
            'type' => $request->input('params.type') || 'img',
            'url' => $fileUrl,
            'alt' => $fileName,
            'width' => $data[0],
            'height' => $data[1],
        ]);
        PostMedia::create(['post_id' => $request->input('params.id'), 'media_id' => $media['id']]);
        return response()->json($media, 202);
    }

    /**
     * Mostrar el recurso especificado.
     *
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $post = Post::find($id);
        $post['user'] = User::select(
            'id',
            'name',
            'media_id'
        )->where('id', $post['user_id'])->first();
        if ($post['user']['media_id']) {
            $post['user']['media'] = Media::find($post['user']['media_id']);
        }
        if ($post['direction_id']) {
            $post['direction'] = Direction::find($post['direction_id']);
        }
        $post['media'] = PostMedia::select('media.*')->where('post_media.post_id', $post['id'])
        ->join('media', 'post_media.media_id', 'media.id')->get();
        return response()->json($post, 200);
    }

    /**
     * Actualizar el recurso especificado en el almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $post = Post::find($id);
        if ($post['user_id'] == $request->user()['id']) {
            if ($request->get('user_id')) {
                $this->validate($request, ['user_id' => 'number',]);
                $post['user_id'] = $request->get('user_id');
            }
            if ($request->get('description')) {
                $this->validate($request, ['description' => 'string',]);
                $post['description'] = $request->get('description');
            }
            // 'found' | 'lost' | 'on_adoption' | 'on_sale' | 'on_hold' | 'other'
            if ($request->get('state')) {
                $this->validate($request, ['state' => 'string',]);
                $post['state'] = $request->get('state');
            }
            $direction;
            if ($post['direction_id']) {
                $this->validate($request, ['direction_id' => 'numeric',]);
                if ($request->get('direction')) {
                    $direction = Direction::find($post['direction_id']);
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
                    $post['direction_id'] = $direction['id'];
                }
            }
            $post->save();
            if ($direction) {
                $post['direction'] = $direction;
            }
            $post['user'] = User::select(
                'id',
                'name',
                'media_id'
            )->where('id', $post['user_id'])->first();
            if ($post['user']['media_id']) {
                $post['user']['media'] = Media::find($post['user']['media_id']);
            }
            $post['media'] = PostMedia::select('media.*')->where('post_media.post_id', $post['id'])
            ->join('media', 'post_media.media_id', 'media.id')->get();
            return response()->json($post, 201);
        } else {
            return response()->json('SERVER.WRONG_USER', 404);
        }
    }
    /**
     * Eliminar el recurso especificado del almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyFile(Request $request, $id) {
        $postMedia = PostMedia::where('media_id', $id)->first();
        $post = Post::find($postMedia['post_id']);
        if ($post['user_id'] == $request->user()['id']) {
            $media = Media::find($postMedia['media_id']);
            if (parse_url($media['url'])['host'] == parse_url(URL::to('/'))['host']) {
                File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($media['url'])['path']);
            }
            $postMedia->delete();
            $media->delete();
            return response()->json(null, 204);
        } else {
            return response()->json('SERVER.WRONG_USER', 404);
        }
    }
    /**
     * Eliminar el recurso especificado del almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
        $post = Post::find($id);
        if ($post['user_id'] == $request->user()['id']) {
            if ($post['direction_id']) {
                Direction::find($post['direction_id'])->delete();
            }
            $post['media'] = PostMedia::select('media.*')->where('post_media.post_id', $post['id'])
            ->join('media', 'post_media.media_id', 'media.id')->get();
            foreach ($post['media'] as &$media) {
                if (parse_url($media['url'])['host'] == parse_url(URL::to('/'))['host']) {
                    File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($media['url'])['path']);
                }
                $media->delete();
            }
            $post->delete();
            return response()->json(null, 204);
        } else {
            return response()->json('SERVER.WRONG_USER', 404);
        }
    }
}
