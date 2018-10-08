<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\Bulletin;
use App\Models\Media;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;

class BulletinController extends BaseController
{
    /**
     * Crear un nuevo registro.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function createBulletin(Request $request) {
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required',
            'date' => 'required',
        ]);
        $bulletin = Bulletin::create([
            'title' => $request->get('title'),
            'description' => $request->get('description'),
        ]);
        if ($request->get('media.url')) {
            $media = Media::create([
                'url' => $request->get('media.url'),
                'alt' => 'bulletin',
            ]);
            $bulletin['media_id'] = $media['id'];
            $bulletin->save();
        }
        return response()->json($bulletin, 202);
    }
    /**
     * Recupera Un registro.
     *
     * @param  number $id
     * @return \Illuminate\Http\Response
     */
    function showOneBulletinById($id) {
        $bulletin = Bulletin::find($id);
        return response()->json($bulletin, 200);
    }
    /**
     * Recupera Un registro.
     *
     * @param  number $id
     * @return \Illuminate\Http\Response
     */
    function showBulletins() {
        $bulletins = Bulletin::orderBy('created_at', 'desc')->paginate(6);
        foreach ($bulletins as &$bulletin) {
            if ($bulletin['media_id']) {
                $bulletin['media'] = Media::find($bulletin['media_id']);
            }
        }
        return response()->json($bulletins, 200);
    }
    /**
     * Actualizar un registro.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function updateBulletin(Request $request) {
        $this->validate($request, ['id' => 'required',]);
        $bulletin = Bulletin::find($id);
        if ($request->get('title')) {
            $this->validate($request, ['title' => 'required|max:255',]);
            $bulletin['title'] = $request->get('title');
        }
        if ($request->get('description')) {
            $this->validate($request, ['description' => 'required',]);
            $bulletin['description'] = $request->get('description');
        }
        if ($request->get('date')) {
            $this->validate($request, ['date' => 'required',]);
            $bulletin['date'] = $request->get('date');
        }
        $user->save();
        return response()->json($user, 201);
    }
    /**
     * Eliminar un registro.
     *
     * @param  number $id
     * @return \Illuminate\Http\Response
     */
    function deleteBulletin($id) {
        $bulletin = Bulletin::find($id);
        $bulletin->delete();
        return response()->json('SERVER.READING_DELETED', 200);
    }
    /**
     * Guarda un archivo en nuestro directorio local.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function setImg(Request $request)
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
        $path = $_SERVER['DOCUMENT_ROOT'] . env('APP_PUBLIC_URL', '/app') . '/img/bulletins/';
        $fileUrl = URL::to('/') . '/img/bulletins/' . $file_name;
        if (!File::exists($path)) {
            File::makeDirectory($path, 0775, true);
        }
        Image::make($file)->save($path . $file_name);
        if ($request->get('params')['bulletin_id']) {
            $bulletin = Bulletin::find($request->get('params')['bulletin_id']);
            // Evalúa si hay un archivo registrado en el servidor con el mismo nombre para eliminarlo.
            if ($bulletin['media_id']) {
                $bulletin['media'] = Media::find($bulletin['media_id']);
                if (parse_url($bulletin['media']['url'])['host'] == parse_url(URL::to('/'))['host']) {
                    File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($bulletin['media']['url'])['path']);
                }
                $bulletin['media']['url'] = $fileUrl;
                $bulletin['media']->save();
            }
         } else {
            $bulletin = Media::create([
                'url' => $fileUrl,
                'alt' => 'bulletin',
            ]);
            $bulletin['media_id'] = $media['id'];
            $bulletin->save();
        }
        return response()->json($fileUrl, 202);
    }
}
