<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\CompaniesCategories;
use App\Models\Company;
use App\Models\Category;
use App\Models\UserRoles;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;

class CompanyController extends BaseController
{
    /**
     * Evalúa si el nombre de la compañía solicitado no esta ocupado.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function checkCompanyNameDisponibility(Request $request) {
        try {
            $data = $request->json()->all();
            $this->validate($request, [
                'name' => 'required|min:5|max:255'
            ]);
            $company = Company::where('name', $data['name'])->first();
            if (count($company) == 0) {
                return response()->json(['success' => true], 200);
            } else {
                return response()->json(['success' => false], 406);
            }
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Inserta la información para crear una nueva compañía.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function createCompany(Request $request) {
        if ($request->isJson()) {
            try {
                $this->validate($request, [
                    'user_id' => 'required',
                    'category_id' => 'required',
                    'name' => 'required|min:5|max:255'
                ]);
                $data = $request->json()->all();
                $company = Company::create([
                    'user_id' => $data['user_id'],
                    'name' => $data['name'],
                    'category_id' => $data['categorie_id']
                ]);
                $user_reole = UserRoles::create([
                    'role_id' => 1,
                    'user_id' => $data['user_id'],
                    'company_id' => $company['id'],
                ]);
                return response()->json($company, 201);
            } catch (Illuminate\Database\QueryException $error) {
                return response()->json($error, 406);
            }
        }
        return response()->json('SERVER.UNAUTHORIZED', 401);
    }
    /**
     * Recupera los últimos registros de empresas.
     *
     * @param integer $id Es la id de la compañia.
     * @return \Illuminate\Http\Response
     */
    function getCompanyById($company_id) {
        try {
            $company = Company::where('id', $company_id)->first();
            return response()->json($company, 200);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Recupera los últimos registros de empresas.
     *
     * @param integer $number Es la cantidad de empresas a regresar.
     * @return \Illuminate\Http\Response
     */
    function getLastCompaniesByNumber($number) {
        try {
            if ($number > 200) {
                $number = 200;
            }
            $companies = Company::orderBy('id', 'desc')->take($number)->get();
            return response()->json($companies, 200);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Devuelve las compañías que está buscando el usuario.
     *
     * @param string $name Es el posible nombre de las empresas.
     * @return \Illuminate\Http\Response
     */
    function getCompaniesByName($name) {
        try {
            $companies = Company::where('name', 'like', '%' . $name . '%')->take(40)->get();
            return response()->json($companies, 200);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Devuelve todas las categorías disponibles para compañias.
     *
     * @return \Illuminate\Http\Response
     */
    function getCompaniesCategories() {
        $companies = CompaniesCategories::all();
        return response()->json($companies, 200);
    }
    /**
     * Guarda un archivo en nuestro directorio local.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function setFrontImg(Request $request)
    {
        $this->validate($request, [
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
        $path = $_SERVER['DOCUMENT_ROOT'] . '/perrosdelagua_back/img/companies/' . $request->get('company_id') . '/';
        $file_url = URL::to('/') . '/img/companies/' . $request->get('company_id') . '/' . $file_name;
        if (!File::exists($path)) {
            File::makeDirectory($path, 0775, true);
        }
        $company = Company::where('id', $request->get('company_id'))->first();
        Image::make($file)->save($path . $file_name);
        // Evalúa si hay un archivo registrado en el servidor con el mismo nombre para eliminarlo.
        if (parse_url($company->front_img_url)['host'] == parse_url(URL::to('/'))['host']) {
            File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($company->front_img_url)['path']);
        }
        $company->front_img_url = $file_url;
        $company->save();
        return response()->json($file_url, 202);
    }
}
