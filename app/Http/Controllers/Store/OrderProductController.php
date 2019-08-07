<?php

namespace App\Http\Controllers\Store;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\Store\Order;
use App\Models\Store\OrderProduct;
use App\Models\Store\Product;
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

class OrderProductController extends BaseController
{
    /**
     * Mostrar el recurso especificado.
     *
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $orderProducts = OrderProduct::where('order_id', $id)->get();
        $orderProducts['products'] = [];
        foreach ($orderProducts as &$orderProduct) {
            $product = Product::find($orderProduct['product_id']);
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
            array_push($orderProducts['products'], $product);
        }
        return response()->json($orderProducts, 200);
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
            'order_id' => 'required',
            'product_id' => 'required',
        ]);
        $product = Product::find($request->get('product_id'));
        if ($product) {
            $order = Order::find($request->get('order_id'));
            if ($order) {
                if ($order['state'] == 'on_create' || $order['state'] == 'stored') {
                    if (!($order['location_id'] &&
                    $order['location_id'] != Warehouse::find($product['warehouse_id'])['direction_id'])) {
                        return $this->storeOrderProduct($request, $order, $product);
                    }
                    return response()->json('SERVER.WAREHOUSES_NOT_MATCH', 409);
                }
                return response()->json('SERVER.ORDER_HAS_OUT', 406);
            }
            return response()->json('SERVER.ORDER_NOT_FOUND', 404);
        }
        return response()->json('SERVER.PRODUCT_NOT_FOUND', 404);
    }

    private function storeOrderProduct(Request $request, $order, $product) {
        $orderProduct = OrderProduct::where([
            'order_id' => $request->get('order_id'),
            'product_id' => $request->get('product_id')
        ])->first();
        $amount = ($request->get('amount') !== null) ? $request->get('amount') : 1;
        if ($amount > $product['amount']) {
            $amount = $product['amount'];
        }
        if ($orderProduct) {
            $orderProduct['amount'] += $amount;
            $orderProduct['price'] = $product['price'];
            $product['amount'] -= $amount;
            $product->save();
            $orderProduct->save();
            return response()->json($orderProduct, 202);
        }
        $orderProduct = OrderProduct::create([
            'order_id' => $request->get('order_id'),
            'product_id' => $request->get('product_id'),
            'amount' => $amount,
            'price' => $product['price'],
        ]);
        $product['amount'] -= $amount;
        $product->save();
        if (OrderProduct::where('order_id', $request->get('order_id'))->count() == 1
        || !$order['location_id']) {
            $order['location_id'] = Warehouse::find($product['warehouse_id'])['direction_id'];
            $order->save();
        }
        return response()->json($orderProduct, 201);
    }
    /**
     * Actualizar el recurso especificado en el almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $orderProduct = OrderProduct::find($id);
        $product = Product::find($orderProduct['product_id']);
        if ($product) {
            $order = Order::find($request->get('order_id'));
            if ($order) {
                if ($order['state'] == 'on_create' || $order['state'] == 'stored') {
                    if ($request->get('amount')) {
                        $this->validate($request, ['amount' => 'numeric',]);
                        $amount = $request->get('amount') || 1;
                        if ($amount > $product['amount']) {
                            $amount = $product['amount'];
                        }
                        $orderProduct['amount'] = $request->get('amount');
                    }
                    $orderProduct['price'] = $product['price'];
                    $orderProduct->save();
                    $product['amount'] -= $amount;
                    $product->save();
                    return response()->json($orderProduct, 202);
                }
                return response()->json('SERVER.ORDER_HAS_OUT', 406);
            }
            return response()->json('SERVER.ORDER_NOT_FOUND', 404);
        }
        return response()->json('SERVER.PRODUCT_NOT_FOUND', 404);
    }
    /**
     * Eliminar el recurso especificado del almacenamiento.
     *
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $orderProduct = OrderProduct::find($id);
        if ($orderProduct) {
            $product = Product::find($orderProduct['product_id']);
            if ($product) {
                $order = Order::find($orderProduct['order_id']);
                if ($order) {
                    if ($order['state'] == 'on_create' || $order['state'] == 'stored') {
                        $product['amount'] += $orderProduct['amount'];
                        if (OrderProduct::where('order_id', $orderProduct['order_id'])->count() == 1) {
                            $order['location_id'] = null;
                            $order->save();
                        }
                        $orderProduct->delete();
                        $product->save();
                        return response()->json(null, 204);
                    }
                    return response()->json('SERVER.ORDER_HAS_OUT', 406);
                }
                return response()->json('SERVER.ORDER_NOT_FOUND', 404);
            }
            return response()->json('SERVER.PRODUCT_NOT_FOUND', 404);
        }
        return response()->json('SERVER.ORDER_PRODUCT_NOT_FOUND', 404);
    }
}
