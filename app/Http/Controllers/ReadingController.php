<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\Reading;

use Illuminate\Http\Request;

class ReadingController extends BaseController
{
    /**
     * Crear un nuevo registro.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function createReading(Request $request) {
        $this->validate($request, [
            'user_id' => 'required',
            'date' => 'required',
            'token' => 'required',
        ]);
        $reading = Reading::create([
            'user_id' => $request->get('user_id'),
            'date' => $request->get('date'),
            'token' => $request->get('token'),
        ]);
        return response()->json($reading, 202);
    }
    /**
     * Recupera Un registro.
     *
     * @param  number $id
     * @return \Illuminate\Http\Response
     */
    function showOneReadingById($id) {
        $reading = Reading::find($id);
        return response()->json($reading, 200);
    }
    /**
     * Actualizar un registro.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function updateReading(Request $request) {
        $this->validate($request, ['id' => 'required',]);
        $reading = Reading::find($id);
        if ($request->get('user_id')) {
            $this->validate($request, ['user_id' => 'required',]);
            $reading->user_id = $request->get('user_id');
        }
        if ($request->get('date')) {
            $this->validate($request, ['date' => 'required',]);
            $reading->date = $request->get('date');
        }
        if ($request->get('token')) {
            $this->validate($request, ['token' => 'required',]);
            $reading->token = $request->get('token');
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
    function deleteReading($id) {
        $reading = Reading::find($id);
        $reading->delete();
        return response()->json('SERVER.READING_DELETED', 200);
    }
}
