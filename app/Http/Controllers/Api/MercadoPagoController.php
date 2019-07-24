<?php

namespace App\Http\Controllers\Api;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use MercadoPago;

class MercadoPagoController extends BaseController
{
    /**
     * Llama la información de lo que el usuario está comprando.
     * 
     * @see https://github.com/mercadopago/dx-php
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function createPay(Request $request) {
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->post('https://api.mercadopago.com/v1/payments?access_token=' . 
            env('MP_ACCESS_TOKEN'), [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode([
                    'payer' => [
                        'email' => $request->get('email')
                    ],
                    'token' => $request->get('id'),
                    'description' => 'Title of what you are paying for',
                    'installments' => $request->get('installments'),
                    'issuer_id' => $request->get('issuer_id'),
                    'payment_method_id' => $request->get('payment_method_id'),
                    'transaction_amount' => 100
                ])
            ]);
            return response()->json($response, 202);
        } catch (\GuzzleHttp\Exception\BadResponseException $error) {
            return response($error->getResponse()->getBody(), 406);
        }
    }

    public function generatePaymentGateway(Request $request)
    {
        MercadoPago\SDK::setClientId(env('MP_CLIENT_ID'));
        MercadoPago\SDK::setClientSecret(env('MP_CLIENT_SECRET'));

        // $user = $request->user();
        $payer = new MercadoPago\Payer();
        $payer->email = 'jlozoya1995@gmail.com';
        
        $preference = new MercadoPago\Preference();
        $preference->external_reference = '1';
        $preference->payer = $payer;
        $preference->back_urls = [];
        $preference->notification_url = env('MP_NOTIFICATION_URL');

        $items = [];

        $item = new MercadoPago\Item();
        $item->id = '1';
        $item->category_id = '1';
        $item->title = 'Item'; 
        $item->description = 'Un item';
        $item->picture_url = 'https://lozoya.biz';
        $item->quantity = 1;
        $item->currency_id = 'MXN';
        $item->unit_price = 5.00;
        array_push($items, $item);

        $preference->items = $items;
    
        $preference->save();

        return $preference->init_point;
    }

    public function ipnNotification(Request $request)
    {
        $mp = new MP (env('MP_CLIENT_ID'), env('MP_CLIENT_SECRET'));
    
        if ( ! isset($_GET["id"], $_GET["topic"]) || ! ctype_digit($_GET["id"])) {
            abort(404);
        }
    
        // Get the payment and the corresponding merchant_order reported by the IPN.
        if ($_GET["topic"] == 'payment') {
            $payment_info = $mp->get("/collections/notifications/" . $_GET["id"]);
            $merchant_order_info = $mp->get("/merchant_orders/" . $payment_info["response"]["collection"]["merchant_order_id"]);
            
            // Get the merchant_order reported by the IPN.
    
            // get order and link the notification id
            $external_reference_id = $merchant_order_info["response"]["external_reference"];
           //here you must clear unnecessary data in external reference
           
           // get order
            $order = Order::findOrFail($external_reference_id);
            // link notification id
            $order->mp_notification_id = $_GET["id"];
    
            if ($merchant_order_info["status"] == 200) {
                // If the payment's transaction amount is equal (or bigger) than the merchant_order's amount you can release your items
                
                $paid_amount = 0;
    
                foreach ($merchant_order_info["response"]["payments"] as $payment) {
                    $order->status = $payment['status'];
                    if ($payment['status'] == 'approved') {
                        $paid_amount += $payment['transaction_amount'];
                    }
                }
    
                if ($paid_amount >= $merchant_order_info["response"]["total_amount"]) {
                    if (count($merchant_order_info["response"]["shipments"]) > 0) { 
                        
                        // The merchant_order has shipments
                        if ($merchant_order_info["response"]["shipments"][0]["status"] == "ready_to_ship") {
                            print_r("Totally paid. Print the label and release your item.");
                        }
                    } else {
                        // The merchant_order don't has any shipments
                        print_r("Totally paid. Release your item.");
                    }
                } else {
                    print_r("Not paid yet. Do not release your item.");
                }
            }
    
            $order->save();
    
        }
    
        return response('OK', 201);
    }
}