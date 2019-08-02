<?php

namespace App\Http\Controllers\Api;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

class PayPalController extends BaseController
{
    public function webcheckout(Request $request)
    {
        $environment = new SandBoxEnvironment(env('PAYPAL_CLIENT_ID'), env('PAYPAL_CLIENT_SECRET'));
        $client = new PayPalHttpClient($environment);
        // Construct a request object and set desired parameters
        // Here, OrdersCreateRequest() creates a POST request to /v2/checkout/orders
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "reference_id" => "test_ref_id1",
                "amount" => [
                    "value" => "10.00",
                    "currency_code" => "MXN"
                ]
            ]],
            "application_context" => [
                "cancel_url" => "https://lozoya.biz/logger/paypal/cancel",
                "return_url" => "https://lozoya.biz/logger/paypal/return"
            ] 
        ];
        try {
            // Call API with your client and get a response for your call
            $response = $client->execute($request);
            
            // If call returns body in response, you can get the deserialized version from the result attribute of the response
            print_r($response);
        } catch (HttpException $ex) {
            echo $ex->statusCode;
            print_r($ex->getMessage());
        }
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