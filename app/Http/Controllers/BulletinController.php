<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\Bulletin;

use Illuminate\Http\Request;

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
        $bulletins = Bulletin::all()->paginate(6);
        foreach ($bulletins as &$bulletin) {
            if ($bulletin['media_id']) {
                $bulletin['media'] = Media::find($bulletin['media_id']);
            }
        }
        return response()->json($bulletin, 200);
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
}
