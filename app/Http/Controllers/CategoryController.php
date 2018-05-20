<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends BaseController
{
    /**
     * Inserta la información para crear una nueva categaria.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function createCategory(Request $request) {
        if ($request->isJson()) {
            try {
                $this->validate($request, [
                    'name' => 'required|min:5|max:255',
                    'about' => 'required',
                    'company_id' => 'required',
                ]);
                $data = $request->json()->all();
                $category = Category::create([
                    'name' => $data['name'],
                    'about' => $data['about'],
                    'company_id' => $data['company_id']
                ]);
                return response()->json($category, 201);
            } catch (Illuminate\Database\QueryException $error) {
                return response()->json($error, 406);
            }
        }
        return response()->json('SERVER.UNAUTHORIZED', 401);
    }
    /**
     * Recupera los registros de categorias de una empresa.
     *
     * @param integer $company_id Es la id de la categoría.
     * @return \Illuminate\Http\Response
     */
    function getAllCategoriesByCompanyId($company_id) {
        try {
            $categories = Category::where('company_id', $company_id)->get();
            return response()->json($categories, 200);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Recupera la categoría seleccionada.
     *
     * @param int $company_id Es la id de la categoría.
     * @return \Illuminate\Http\Response
     */
    function getCategoryById($id) {
        try {
            $category = Category::find($id);
            return response()->json($category, 200);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Devuelve las compañías que está buscando el usuario.
     *
     * @param string $criteria Es el parámetro de lo que el usuario está buscando.
     * @return \Illuminate\Http\Response
     */
    function getCategoriesByName($name) {
        try {
            $categories = Category::where('name', 'like', '%' . $name . '%')->take(40)->get();
            return response()->json($categories, 200);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Actualiza la información de una categoría.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function updateCategory(Request $request) {
        $this->validate($request, [
            'id' => 'required',
            'name' => 'required',
            'about' => 'required',
        ]);
        try {
            $category = Category::find($request->get('id'));
            $category->name = $request->get('name');
            $category->about = $request->get('about');
            $category->save();
            return response()->json($category, 202);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Guarda un archivo en nuestro directorio local.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function setCategoryImg(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'company_id' => 'required',
            'file_name' => 'required',
            'type' => 'required'
        ]);
        $file_name = $request->get('file_name');
        if ($request['type'] == 'base64') {
            $file = base64_decode(explode(',', $request['file'])[1]);
        } else {
            $file = $request->file('file');
        }
        $path = $_SERVER['DOCUMENT_ROOT'] . '/perrosdelagua_back/img/companies/' . $request->get('company_id') . '/categories/';
        $file_url = URL::to('/') . '/img/companies/' . $request->get('company_id') . '/categories/' . $file_name;
        if (!File::exists($path)) {
            File::makeDirectory($path, 0775, true);
        }
        $category = Category::where('id', $request->get('category_id'))->first();
        Image::make($file)->save($path . $file_name);
        // Evalúa si hay un archivo registrado en el servidor con el mismo nombre para eliminarlo.
        if (parse_url($category->img_url)['host'] == parse_url(URL::to('/'))['host']) {
            File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($category->img_url)['path']);
        }
        $category->img_url = $file_url;
        $category->save();
        return response()->json($file_url, 202);
    }
    /**
     * Elimina la información de una categoría.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    function deleteCategory($id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->delete();
            return response()->json($category, 200);
        } else {
            return response()->json('SERVER.CATEGORY_NOT_FOUND', 404);
        }
    }
}
