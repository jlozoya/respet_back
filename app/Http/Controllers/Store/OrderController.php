<?php

namespace App\Http\Controllers\Store;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\Store\Order;
use App\Models\Store\OrderProduct;
use App\Models\Store\Warehouse;
use App\Models\Store\Product;
use App\Models\Store\ProductMedia;
use App\Models\User\User;
use App\Models\Generic\Media;
use App\Models\Generic\Direction;
use DB;

use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;

use Carbon\Carbon;

class OrderController extends BaseController
{
    /**
     * Muestra una lista del recurso.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $user = $request->user();
        if ($user['role'] == 'roundsman') {
            $orders = Order::where('roundsman_id', $user['id'])
            ->where('state', '!=', 'on_create');
            if ($request->get('roundsman_id')) {
                $orders = $orders->where('roundsman_id', $request->get('roundsman_id'));
            }
            $orders = $orders->orderBy('updated_at', 'DESC')->paginate(8);
            return $this->attachData($orders);
        }
        if ($request->get('search')) {
            $orders = Order::where('state', 'like', '%' . $request->get('search') . '%');
            if ($request->get('roundsman_id')) {
                $orders = $orders->where('roundsman_id', $request->get('roundsman_id'));
            }
            $orders = $orders->orderBy('updated_at', 'DESC')->paginate(8);
            return $this->attachData($orders);
        } else if ($request->get('direction')) {
            $where = "";
            if ($request->input('direction.country')) {
                $where .= " `directions`.`country` LIKE '%" . $request->input('direction.country') . "%'";
            }
            if ($request->input('direction.administrative_area_level_1')) {
                $where .= " `directions`.`administrative_area_level_1` LIKE '%" . $request->input('direction.administrative_area_level_1') . "%'";
            }
            if ($request->input('direction.administrative_area_level_2')) {
                $where .= " `directions`.`administrative_area_level_2` LIKE '%" . $request->input('direction.administrative_area_level_2') . "%'";
            }
            if ($request->input('direction.route')) {
                $where .= " `directions`.`route` LIKE '%" . $request->input('direction.route') . "%'";
            }
            if ($request->input('direction.street_number')) {
                $where .= " `directions`.`street_number` LIKE '%" . $request->input('direction.street_number') . "%'";
            }
            if ($request->input('direction.postal_code')) {
                $where .= " `directions`.`postal_code` LIKE '%" . $request->input('direction.postal_code') . "%'";
            }
            if ($request->input('direction.lat')) {
                $where .= " `directions`.`lat` LIKE '%" . $request->input('direction.lat') . "%'";
            }
            if ($request->input('direction.lng')) {
                $where .= " `directions`.`lng` LIKE '%" . $request->input('direction.lng') . "%'";
            }
            $orders = Order::select('orders.*');
            if ($request->get('roundsman_id')) {
                $orders->where('orders.roundsman_id', $request->get('roundsman_id'));
            }
            $orders->join('directions', 'orders.location_id', '=', 'directions.id')
            ->join('directions', 'orders.destination_id', '=', 'directions.id')
            ->whereRaw($where)
            ->orderBy('updated_at', 'DESC')
            ->paginate(8);
            return $this->attachData($orders);
        } else if ($request->get('latLng')) {
            $latlng = explode(',', $request->get('latLng'));
            $distance = 10;
            $directions = DB::table('directions')
            ->select(DB::raw("`id`, (acos(sin(radians($latlng[0])) * sin(radians(`lat`)) + 
            cos(radians($latlng[0])) * cos(radians(`lat`)) * 
            cos(radians($latlng[1]) - radians(`lng`))) * 6378) as 
            `distance`"))
            ->havingRaw("distance <= $distance")
            ->where('lat', '!=', 0)
            ->get()->toArray();
            $orders = [];
            foreach ($directions as &$direction) {
                $order = Order::where('location_id', $direction['id'])
                ->orWhere('destination_id', $direction['id'])->first();
                if ($order) {
                    array_push($orders, $order);
                }
            }
            return $this->attachData($orders);
        } else {
            $orders = Order::orderBy('updated_at', 'DESC');
            if ($request->get('roundsman_id')) {
                $orders->where('roundsman_id', $request->get('roundsman_id'));
            }
            return $this->attachData($orders->paginate(8));
        }
    }

    /**
     * Agrega información a la consulta.
     * 
     * @param $products
     * @return \Illuminate\Http\Response
     */
    private function attachData($orders) {
        foreach ($orders as &$order) {
            if ($order['user_id']) {
                $order['user'] = User::select(
                    'id',
                    'name',
                    'media_id'
                )->where('id', $order['user_id'])->first();
                if ($order['user']['media_id']) {
                    $order['user']['media'] = Media::find($order['user']['media_id']);
                }
            }
            if ($order['roundsman_id']) {
                $order['roundsman'] = User::select(
                    'id',
                    'name',
                    'media_id'
                )->where('id', $order['roundsman_id'])->first();
                if ($order['roundsman']['media_id']) {
                    $order['roundsman']['media'] = Media::find($order['roundsman']['media_id']);
                }
            }
            if ($order['location_id']) {
                $order['location'] = Direction::find($order['location_id']);
            }
            if ($order['destination_id']) {
                $order['destination'] = Direction::find($order['destination_id']);
            }
        }
        return response()->json($orders, 200);
    }

    /**
     * Almacenar un recurso recién creado en el almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $user = $request->user();
        $order = Order::create([
            'user_id' => $user['id'],
            'roundsman_id' => $request->get('roundsman_id'),
            'price' => $request->get('price'),
            'state' => $request->get('state') || 'on_create',
            'take_out_date' => $request->get('take_out_date'),
            'delivery_date' => $request->get('delivery_date'),
            'location_id' => $request->get('location_id'),
            'destination_id' => $request->get('destination_id'),
        ]);
        $location = null;
        if ($request->get('location')) {
            $location = Direction::create([
                'country' => $request->input('location.country'),
                'administrative_area_level_1' => $request->input('location.administrative_area_level_1'),
                'administrative_area_level_2' => $request->input('location.administrative_area_level_2'),
                'route' => $request->input('location.route'),
                'street_number' => $request->input('location.street_number'),
                'postal_code' => $request->input('location.postal_code'),
                'lat' => $request->input('location.lat'),
                'lng' => $request->input('location.lng'),
            ]);
            $order['location_id'] = $location['id'];
            $order->save();
        } else if ($order['location_id']) {
            $location = Direction::find($order['location_id']);
        }
        $destination = null;
        if ($request->get('destination')) {
            $destination = Direction::create([
                'country' => $request->input('destination.country'),
                'administrative_area_level_1' => $request->input('destination.administrative_area_level_1'),
                'administrative_area_level_2' => $request->input('destination.administrative_area_level_2'),
                'route' => $request->input('destination.route'),
                'street_number' => $request->input('destination.street_number'),
                'postal_code' => $request->input('destination.postal_code'),
                'lat' => $request->input('destination.lat'),
                'lng' => $request->input('destination.lng'),
            ]);
            $order['destination_id'] = $destination['id'];
            $order->save();
        } else if ($order['destination_id']) {
            $destination = Direction::find($order['destination_id']);
        }
        if ($location) {
            $order['location'] = $location;
        } 
        if ($destination) {
            $order['destination'] = $destination;
        }
        $order['user'] = $user;
        if ($order['user']['media_id']) {
            $order['user']['media'] = Media::find($order['user']['media_id']);
        }
        return response()->json($order, 201);
    }

    private function fillOrderData($order) {
        $order['user'] = User::select(
            'id',
            'name',
            'media_id'
        )->find($order['user_id']);
        if ($order['user']['media_id']) {
            $order['user']['media'] = Media::find($order['user']['media_id']);
        }
        if ($order['roundsman_id']) {
            $order['roundsman'] = User::select(
                'id',
                'name',
                'media_id'
            )->find($order['roundsman_id']);
            if ($order['roundsman']['media_id']) {
                $order['roundsman']['media'] = Media::find($order['roundsman']['media_id']);
            }
        }
        if ($order['location_id']) {
            $order['location'] = Direction::find($order['location_id']);
        }
        if ($order['destination_id']) {
            $order['destination'] = Direction::find($order['destination_id']);
        }
        $order['products'] = OrderProduct::where('order_id', $id)->get();
        foreach ($order['products'] as &$orderProduct) {
            $orderProduct['product'] = Product::find($orderProduct['product_id']);
            if ($orderProduct['product']['warehouse_id']) {
                $orderProduct['product']['warehouse'] = Warehouse::find($orderProduct['product']['warehouse_id']);
                if ($orderProduct['product']['warehouse']['direction_id']) {
                    $orderProduct['product']['warehouse']['direction'] = Direction::find($orderProduct['product']['warehouse']['direction_id']);
                }
                if ($orderProduct['product']['warehouse']['media_id']) {
                    $orderProduct['product']['warehouse']['media'] = Media::find($orderProduct['product']['warehouse']['media_id']);
                }
            }
            $orderProduct['product']['media'] = ProductMedia::select('media.*')->where('product_media.product_id', $orderProduct['product']['id'])
            ->join('media', 'product_media.media_id', 'media.id')->get();
        }
        return $order;
    }
    /**
     * Mostrar el recurso especificado.
     *
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $orser = Order::find($id);
        if ($order) {
            $order = $this->fillOrderData($order);
            return response()->json($order, 200);
        } else {
            return response()->json('ORDER_NOT_FOUND', 404);
        }
    }
    /**
     * Mostrar el recurso especificado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showLastOrder(Request $request) {
        $user = $request->user();
        $order = Order::where(['user_id' => $user['id'], 'state' => 'on_create'])->orderBy('id', 'DESC')->first();
        if ($order) {
            $order = $this->fillOrderData($order);
            return response()->json($order, 200);
        } else {
            return response()->json('ORDER_NOT_FOUND', 404);
        }
    }
    /**
     * Actualizar el recurso especificado en el almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $order = Order::find($id);
        if ($request->get('user_id')) {
            $this->validate($request, ['user_id' => 'numeric',]);
            $order['user_id'] = $request->get('user_id');
        }
        if ($request->get('roundsman_id')) {
            $this->validate($request, ['roundsman_id' => 'numeric',]);
            $order['roundsman_id'] = $request->get('roundsman_id');
        }
        if ($request->get('price')) {
            $this->validate($request, ['price' => 'numeric',]);
            $order['price'] = $request->get('price');
        }
        if ($request->get('state')) {
            $this->validate($request, ['state' => 'in:on_create,stored,on_transit,delivered,other',]);
            $order['state'] = $request->get('state');
        }
        if ($request->get('take_out_date')) {
            $this->validate($request, ['take_out_date' => 'date',]);
            $order['take_out_date'] = $request->get('take_out_date');
        }
        if ($request->get('delivery_date')) {
            $this->validate($request, ['delivery_date' => 'date',]);
            $order['delivery_date'] = $request->get('delivery_date');
        }
        $destination;
        if ($order['destination_id']) {
            if ($request->get('destination')) {
                $destination = Direction::find($order['destination_id']);
                if ($request->get('destination')['country']) {
                    $this->validate($request, ['destination.country' => 'max:60',]);
                    $destination['country'] = $request->get('destination')['country'];
                }
                if ($request->get('destination')['administrative_area_level_1']) {
                    $this->validate($request, ['destination.administrative_area_level_1' => 'max:60',]);
                    $destination['administrative_area_level_1'] = $request->get('destination')['administrative_area_level_1'];
                }
                if ($request->get('destination')['administrative_area_level_2']) {
                    $this->validate($request, ['destination.administrative_area_level_2' => 'max:60',]);
                    $destination['administrative_area_level_2'] = $request->get('destination')['administrative_area_level_2'];
                }
                if ($request->get('destination')['route']) {
                    $this->validate($request, ['destination.route' => 'max:60',]);
                    $destination['route'] = $request->get('destination')['route'];
                }
                if ($request->get('destination')['street_number']) {
                    $destination['street_number'] = $request->get('destination')['street_number'];
                }
                if ($request->get('destination')['postal_code']) {
                    $this->validate($request, ['destination.postal_code' => 'numeric',]);
                    $destination['postal_code'] = $request->get('destination')['postal_code'];
                }
                if ($request->get('destination')['lat']) {
                    $this->validate($request, ['destination.lat' => 'numeric',]);
                    $destination['lat'] = $request->get('destination')['lat'];
                }
                if ($request->get('destination')['lng']) {
                    $this->validate($request, ['destination.lng' => 'numeric',]);
                    $destination['lng'] = $request->get('destination')['lng'];
                }
                $destination->save();
            }
        } else {
            if ($request->get('destination')) {
                $destination = Direction::create([
                    'country' => $request->input('destination.country'),
                    'administrative_area_level_1' => $request->input('destination.administrative_area_level_1'),
                    'administrative_area_level_2' => $request->input('destination.administrative_area_level_2'),
                    'route' => $request->input('destination.route'),
                    'street_number' => $request->input('destination.street_number'),
                    'postal_code' => $request->input('destination.postal_code'),
                    'lat' => $request->input('destination.lat'),
                    'lng' => $request->input('destination.lng'),
                ]);
                $order['destination_id'] = $destination['id'];
            }
        }
        if ($order['state'] == 'on_create' && $order['roundsman_id'] && $order['take_out_date']
        && $order['delivery_date'] && $order['location_id'] && $order['destination_id']) {
            $order['state'] = 'stored';
        }
        $order->save();
        if ($order['location_id']) {
            $order['location'] = Direction::find($order['location_id']);
        }
        if ($destination) {
            $order['destination'] = $destination;
        }
        if ($order['roundsman_id']) {
            $order['roundsman'] = User::select(
                'id',
                'name',
                'media_id',
                'lang'
            )->find($order['roundsman_id']);
            //$order['roundsman']->notify(new OrderHasUpdated($confirmationLink, $order['roundsman']['lang']));
            if ($order['roundsman']['media_id']) {
                $order['roundsman']['media'] = Media::find($order['roundsman']['media_id']);
            }
        }
        $order['user'] = User::select(
            'id',
            'name',
            'media_id'
        )->find($order['user_id']);
        if ($order['user']['media_id']) {
            $order['user']['media'] = Media::find($order['user']['media_id']);
        }
        return response()->json($order, 202);
    }
    /**
     * Actualiza el estado del stado de una orden.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function updateState(Request $request, $id) {
        $this->validate($request, ['state' => 'required|in:on_create,stored,on_transit,delivered,other',]);
        $order = Order::find($id);
        if ($order) {
            $order['state'] = $request->get('state');
            $order->save();
            return response()->json($order, 202);
        }
        return response()->json('SERVER.ORDER_NOT_FOUND', 404);
    }
    /**
     * Eliminar el recurso especificado del almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  number  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
        $order = Order::find($id);
        if ($order) {
            if ($order['state'] == 'on_create' || $order['state'] == 'stored') {
                if (OrderProduct::where('order_id', $order['id'])->count() > 1) {
                    $order->delete();
                    return response()->json(null, 204);
                }
                return response()->json('SERVER.ORDER_NOT_EMPTY', 406);
            }
            return response()->json('SERVER.ORDER_HAS_OUT', 406);
        }
        return response()->json('SERVER.ORDER_NOT_FOUND', 404);
    }
}
