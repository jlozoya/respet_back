<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\Beat;

use Illuminate\Http\Request;

class BeatController extends BaseController
{
    /**
     * Crear un nuevo registro.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function createBeat(Request $request) {
        $this->validate($request, [
            'reading_id' => 'required',
            'time' => 'required',
            'token' => 'required',
        ]);
        $beat = Beat::create([
            'reading_id' => $request->get('reading_id'),
            'time' => $request->get('time'),
            'beat' => $request->get('beat'),
        ]);
        return response()->json($beat, 202);
    }
    /**
     * Recupera Un registro.
     *
     * @param  number $id
     * @return \Illuminate\Http\Response
     */
    function showOneBeatById($id) {
        $beat = Beat::find($id);
        return response()->json($beat, 200);
    }
    /**
     * Actualizar un registro.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function updateBeat(Request $request) {
        $this->validate($request, ['id' => 'required',]);
        $beat = Beat::find($id);
        if ($request->get('reading_id')) {
            $this->validate($request, ['reading_id' => 'required',]);
            $beat->reading_id = $request->get('reading_id');
        }
        if ($request->get('time')) {
            $this->validate($request, ['time' => 'required',]);
            $beat->time = $request->get('time');
        }
        if ($request->get('beat')) {
            $this->validate($request, ['beat' => 'required',]);
            $beat->beat = $request->get('beat');
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
    function deleteBeat($id) {
        $beat = Beat::find($id);
        $beat->delete();
        return response()->json('SERVER.READING_DELETED', 200);
    }
}
