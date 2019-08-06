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
        MercadoPago\SDK::setAccessToken(env('MP_ACCESS_TOKEN'));

        // $user = $request->user();
        $payer = new MercadoPago\Payer();
        $payer->name = 'APRO';
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
        $item->unit_price = 10.00;
        array_push($items, $item);

        $preference->items = $items;
    
        $preference->save();

        return $preference->init_point;
    }

    public function ipnNotification(Request $request)
    {
        MercadoPago\SDK::setClientId(env('MP_CLIENT_ID'));
        MercadoPago\SDK::setClientSecret(env('MP_CLIENT_SECRET'));

        $filters = array(
            'id' => '1126894556'
        );
        
        $payment = MercadoPago\Payment::search();
    
        return response()->json($payment, 200);;
    }
}