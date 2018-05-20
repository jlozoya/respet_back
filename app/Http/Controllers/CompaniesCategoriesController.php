<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\CompaniesCategories;
use Illuminate\Http\Request;

class CompaniesCategoriesController extends BaseController
{
    /**
     * Crea un nueva organización.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function createCompaniesCategories(Request $request) {
        $this->validate($request, [
            'user_id' => 'required',
            'name' => 'required|min:5|max:255'
        ]);
        try {
            $data = $request->json()->all();
            $companyCategory = CompaniesCategories::create([
                'user_id' => $data['user_id'],
                'companyCategory_token' => str_random(60),
                'name' => $data['name'],
            ]);
            return response()->json($companyCategory, 201);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Recupera una organización por su id.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    function getCompaniesCategories($id) {
        try {
            $companyCategory = CompaniesCategories::find($id);
            return response()->json($companyCategory, 200);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Devuelve todas las categorías disponibles para compañias.
     *
     * @return \Illuminate\Http\Response
     */
    function getAllCompaniesCategories() {
        $companies = CompaniesCategories::all();
        return response()->json($companies, 200);
    }
    /**
     * Actualiza la organización.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function updateCompaniesCategories(Request $request) {
        $this->validate($request, [
            'id' => 'required',
        ]);
        try {
            $companyCategory = CompaniesCategories::find($request->get('id'));
            if ($request->get('user_id')) {
                $companyCategory->user_id = $request->get('user_id');
            }
            if ($request->get('companyCategory_token')) {
                $companyCategory->companyCategory_token = $request->get('companyCategory_token');
            }
            $companyCategory->save();
            return response()->json($companyCategory, 201);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Elimina la información de una organización.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    function deleteCompaniesCategories($id)
    {
        $companyCategory = CompaniesCategories::find($id);
        if ($companyCategory) {
            $companyCategory->delete();
            return response()->json($companyCategory, 200);
        } else {
            return response()->json('SERVER.ORGANIZATION_NOT_FOUND', 404);
        }
    }
}
