<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;

use Illuminate\Http\Request;

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

    public function generatePaymentGateway()
    {
        $mp = new MP (env('MP_ACCESS_TOKEN'), env('MP_CLIENT_SECRET'));

        $current_user = auth()->user();

        $preferenceData = [
            'external_reference' => $this->id,
            // also you can do this
            'external_reference' => $this->prefix . $this->id,
            'payer'              => [
                //
            ],
            'back_urls'          => [
                //
            ],
            'notification_url'   => env('MP_NOTIFICATION_URL')
        ];

        // add items
        foreach ($this->items as $item) {
            $preferenceData['items'][] = [
                'id'          => '...',
                'category_id' => '...',
                'title'       => '...',      
                'description' => '...',
                'picture_url' => '...',
                'quantity'    => '...',
                'currency_id' => '...',
                'unit_price'  => '...',
            ];
        }
        $preference = $mp->create_preference($preferenceData);
        // return init point to be redirected
        return $preference['response']['init_point'];
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