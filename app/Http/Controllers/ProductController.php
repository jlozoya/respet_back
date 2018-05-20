<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends BaseController
{
    /**
     * Inserta la información para crear un nuevo producto.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function createProduct(Request $request) {
        if ($request->isJson()) {
            try {
                $this->validate($request, [
                    'category_id' => 'required',
                    'name' => 'required',
                    'about' => 'required',
                    'price' => 'required',
                ]);
                $data = $request->json()->all();
                $product = Product::create([
                    'category_id' => $data['category_id'],
                    'name' => $data['name'],
                    'about' => $data['about'],
                    'price' => $data['price'],
                    'discount' => $data['discount'],
                    'existences' => $data['existences'],
                ]);
                return response()->json($product, 201);
            } catch (Illuminate\Database\QueryException $error) {
                return response()->json($error, 406);
            }
        }
        return response()->json('SERVER.UNAUTHORIZED', 401);
    }
    /**
     * Recupera el producto seleccionado.
     *
     * @param integer $company_id Es la id del producto.
     * @return \Illuminate\Http\Response
     */
    function getProductById($id) {
        try {
            $product = Product::find($id);
            return response()->json($product, 200);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Recupera los registros de productos segun la id de su categoría.
     *
     * @param integer $category_id Es la id de la categoría.
     * @return \Illuminate\Http\Response
     */
    function getAllProductsByCategoryId($category_id) {
        try {
            $products = Product::where('category_id', $category_id)->get();
            return response()->json($products, 200);
        } catch (Illuminate\Database\QueryException $error) {
            return response()->json($error, 406);
        }
    }
    /**
     * Actualiza la información de un producto.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function updateProduct(Request $request) {
        $this->validate($request, [
            'id' => 'required',
        ]);
        try {
            $product = Product::find($request->get('id'));
            if ($request->get('name')) {
                $product->name = $request->get('name');
            }
            if ($request->get('about')) {
                $product->about = $request->get('about');
            }
            if ($request->get('price')) {
                $product->price = $request->get('price');
            }
            if ($request->get('discount')) {
                $product->discount = $request->get('discount');
            }
            if ($request->get('existences')) {
                $product->existences = $request->get('existences');
            }
            $product->save();
            return response()->json($product, 202);
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
    public function setProductImg(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'file_name' => 'required',
            'type' => 'required'
        ]);
        $category = Category::find($request->get('category_id'));
        $file_name = $request->get('file_name');
        if ($request['type'] == 'base64') {
            $file = base64_decode(explode(',', $request['file'])[1]);
        } else {
            $file = $request->file('file');
        }
        $path = $_SERVER['DOCUMENT_ROOT'] . '/perrosdelagua_back/img/companies/' . $category->company_id . '/categories/';
        $file_url = URL::to('/') . '/img/companies/' . $category->company_id . '/categories/' . $file_name;
        if (!File::exists($path)) {
            File::makeDirectory($path, 0775, true);
        }
        $product = Product::find($request->get('product_id'));
        Image::make($file)->save($path . $file_name);
        // Evalúa si hay un archivo registrado en el servidor con el mismo nombre para eliminarlo.
        if (parse_url($product->img_url)['host'] == parse_url(URL::to('/'))['host']) {
            File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($product->img_url)['path']);
        }
        $product->img_url = $file_url;
        $product->save();
        return response()->json($file_url, 202);
    }
    /**
     * Elimina la información de un producto.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    function deleteProduct($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json($product, 200);
        } else {
            return response()->json('SERVER.PRODUCT_NOT_FOUND', 404);
        }
    }
}
