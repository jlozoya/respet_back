<?php

namespace App\Http\Controllers\Store;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\Store\Product;
use App\Models\Store\OrderProduct;
use App\Models\Store\Warehouse;
use App\Models\Generic\Direction;
use App\Models\Store\ProductMedia;
use App\Models\Generic\Media;
use DB;

use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;

use Carbon\Carbon;

class ProductController extends BaseController
{
    /**
     * Muestra una lista del recurso.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->get('warehouse_id')) {
            if ($request->get('search')) {
                $search = $request->get('search');
                $products = Product::where('warehouse_id', $request->get('warehouse_id'))
                ->where(function ($query) use ($request) {
                    $search = $request->get('search');
                    $query->where('name', 'like', "%$search%");
                    $query->orWhere('description', 'like', "%$search%");
                })->orderBy('updated_at', 'DESC')
                ->orderBy('updated_at', 'DESC')
                ->paginate(8);
                return $this->attachData($products);
            }
            return $this->attachData(Product::where('warehouse_id', $request->get('warehouse_id'))
            ->orderBy('updated_at', 'DESC')->paginate(8));
        }
        if ($request->get('search')) {
            $search = $request->get('search');
            $products = Product::where('name', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->orderBy('updated_at', 'DESC')
            ->paginate(8);
            return $this->attachData($products);
        }
        return $this->attachData(Product::orderBy('updated_at', 'DESC')->paginate(8));
    }

    /**
     * Agrega información a la consulta.
     * 
     * @param $products
     * @return \Illuminate\Http\Response
     */
    private function attachData($products) {
        foreach ($products as &$product) {
            if ($product['warehouse_id']) {
                $product['warehouse'] = Warehouse::find($product['warehouse_id']);
                if ($product['warehouse']['direction_id']) {
                    $product['warehouse']['direction'] = Direction::find($product['warehouse']['direction_id']);
                }
                if ($product['warehouse']['media_id']) {
                    $product['warehouse']['media'] = Media::find($product['warehouse']['media_id']);
                }
            }
            $product['media'] = ProductMedia::select('media.*')->where('product_media.product_id', $product['id'])
            ->join('media', 'product_media.media_id', 'media.id')->get();
        }
        return response()->json($products, 200);
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
            'description' => 'required',
            'amount' => 'required',
            'price' => 'required',
            'warehouse_id' => 'required',
        ]);
        $product = Product::create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'amount' => $request->get('amount'),
            'price' => $request->get('price'),
            'warehouse_id' => $request->get('warehouse_id'),
        ]);
        return response()->json($product, 201);
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
        $path = $_SERVER['DOCUMENT_ROOT'] . env('APP_PUBLIC_URL', '/app') . '/img/products/' . $date . '/';
        $fileUrl = URL::to('/') . '/img/products/' . $date . '/' . $fileName;
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
        ProductMedia::create(['product_id' => $id, 'media_id' => $media['id']]);
        return response()->json($media, 202);
    }

    /**
     * Mostrar el recurso especificado.
     *
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $product = Product::find($id);
        if ($product['warehouse_id']) {
            $product['warehouse'] = Warehouse::find($product['warehouse_id']);
            if ($product['warehouse']['direction_id']) {
                $product['warehouse']['direction'] = Direction::find($product['warehouse']['direction_id']);
            }
            if ($product['warehouse']['media_id']) {
                $product['warehouse']['media'] = Media::find($product['warehouse']['media_id']);
            }
        }
        $product['media'] = ProductMedia::select('media.*')->where('product_media.product_id', $product['id'])
        ->join('media', 'product_media.media_id', 'media.id')->get();
        return response()->json($product, 200);
    }

    /**
     * Actualizar el recurso especificado en el almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $product = Product::find($id);
        if ($product) {
            if ($request->get('description')) {
                $this->validate($request, ['description' => 'string',]);
                $product['description'] = $request->get('description');
            }
            // 'found' | 'lost' | 'on_adoption' | 'on_sale' | 'on_hold' | 'other'
            if ($request->get('state')) {
                $this->validate($request, ['state' => 'string',]);
                $product['state'] = $request->get('state');
            }
            $product->save();
            if ($product['warehouse_id']) {
                $product['warehouse'] = Warehouse::find($product['warehouse_id']);
                if ($product['warehouse']['direction_id']) {
                    $product['warehouse']['direction'] = Direction::find($product['warehouse']['direction_id']);
                }
                if ($product['warehouse']['media_id']) {
                    $product['warehouse']['media'] = Media::find($product['warehouse']['media_id']);
                }
            }
            $product['media'] = ProductMedia::select('media.*')->where('product_media.product_id', $product['id'])
            ->join('media', 'product_media.media_id', 'media.id')->get();
            return response()->json($product, 201);
        }
        return response()->json('SERVER.PRODUCT_NOT_FOUND', 404);
    }
    /**
     * Eliminar el recurso especificado del almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyFile(Request $request, $id) {
        $productMedia = ProductMedia::where('media_id', $id)->first();
        if ($productMedia['media_id']) {
            $media = Media::find($productMedia['media_id']);
            if (parse_url($media['url'])['host'] == parse_url(URL::to('/'))['host']) {
                File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($media['url'])['path']);
            }
            $productMedia->delete();
            $media->delete();
            return response()->json(null, 204);
        }
        return response()->json('SERVER.NOT_FOUND', 404);
    }
    /**
     * Eliminar el recurso especificado del almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
        $product = Product::find($id);
        if (!OrderProduct::where('product_id', $id)->count()) {
            $product['media'] = ProductMedia::select('media.*')->where('product_media.product_id', $product['id'])
            ->join('media', 'product_media.media_id', 'media.id')->get();
            foreach ($product['media'] as &$media) {
                if (parse_url($media['url'])['host'] == parse_url(URL::to('/'))['host']) {
                    File::delete($_SERVER['DOCUMENT_ROOT'] . parse_url($media['url'])['path']);
                }
                $media->delete();
            }
            $product->delete();
            return response()->json(null, 204);
        }
        return response()->json('SERVER.PRODUCT_IN_USE', 406);
    }
}
