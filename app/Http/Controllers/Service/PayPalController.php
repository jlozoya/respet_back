<?php

namespace App\Http\Controllers\Api;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\Invoice;
use App\Models\IPNStatus;
use App\Models\Item;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\AdaptivePayments;
use Srmklive\PayPal\Services\ExpressCheckout;

class PayPalController extends BaseController
{
    /**
     * @var ExpressCheckout
     */
    protected $provider;

    public function __construct()
    {
        $this->provider = new ExpressCheckout();
    }

    public function getIndex(Request $request)
    {
        $response = [];
        if (session()->has('code')) {
            $response['code'] = session()->get('code');
            session()->forget('code');
        }
        if (session()->has('message')) {
            $response['message'] = session()->get('message');
            session()->forget('message');
        }
        return view('welcome', compact('response'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getExpressCheckout(Request $request)
    {
        $recurring = ($request->get('mode') === 'recurring') ? true : false;

        $cart = $this->geCartData($recurring);
        $invoice = $this->createInvoice($cart, 'Registered');
        $cart['invoice_id'] = $invoice['id'];
        
        try {
            $response = $this->provider->setExpressCheckout($cart, $recurring);
            return redirect($response['paypal_link']);
        } catch (\Exception $e) {
            $invoice = $this->createInvoice($cart, 'Invalid');
            return $e;
        }
    }
    /**
     * Procesar el pago en PayPal.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getExpressCheckoutSuccess(Request $request)
    {
        $recurring = ($request->get('mode') === 'recurring') ? true : false;
        $token = $request->get('token');
        $PayerID = $request->get('PayerID');
        
        $response = $this->provider->getExpressCheckoutDetails($token);
        
        $invoice = Invoice::find($response['INVNUM']);
        $cart = $this->getCheckoutData($invoice);
        // Verificar token de Express Checkout
        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
            if ($recurring === true) {
                $response = $this->provider->createMonthlySubscription($response['TOKEN'], 9.99, $cart['subscription_desc']);
                if (!empty($response['PROFILESTATUS']) && in_array($response['PROFILESTATUS'], ['ActiveProfile', 'PendingProfile'])) {
                    $status = 'Processed';
                } else {
                    $status = 'Invalid';
                }
            } else {
                // Realizar transacción en PayPal
                $payment_status = $this->provider->doExpressCheckoutPayment($cart, $token, $PayerID);
                $status = $payment_status['PAYMENTINFO_0_PAYMENTSTATUS'];
            }
            $invoice = $this->updateInvoice($invoice, $status);
            return redirect(env('APP_REDIRECTS_LINK', '../'));
        }
    }
    
    public function getAdaptivePay()
    {
        $this->provider = new AdaptivePayments();
        $data = [
            'receivers'  => [
                [
                    'email'   => 'johndoe@example.com',
                    'amount'  => 10,
                    'primary' => true,
                ],
                [
                    'email'   => 'janedoe@example.com',
                    'amount'  => 5,
                    'primary' => false,
                ],
            ],
            'payer'      => 'EACHRECEIVER', // (Optional) Describes who pays PayPal fees. Allowed values are: 'SENDER', 'PRIMARYRECEIVER', 'EACHRECEIVER' (Default), 'SECONDARYONLY'
            'return_url' => url('payment/success'),
            'cancel_url' => url('payment/cancel'),
        ];
        $response = $this->provider->createPayRequest($data);
        dd($response);
    }
    /**
     * Analizar PayPal IPN.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function notify(Request $request)
    {
        if (!($this->provider instanceof ExpressCheckout)) {
            $this->provider = new ExpressCheckout();
        }
        $post = [
            'cmd' => '_notify-validate',
        ];
        $data = $request->all();
        foreach ($data as $key => $value) {
            $post[$key] = $value;
        }
        $response = (string) $this->provider->verifyIPN($post);
        $ipn = new IPNStatus();
        $ipn->payload = json_encode($post);
        $ipn->status = $response;
        $ipn->save();
    }
    /**
     * Configure los datos del carrito para procesar el pago en PayPal.
     *
     * @param bool $recurring
     *
     * @return array
     */
    protected function geCartData($recurring = false)
    {
        $cart = [];
        $cart['invoice_description'] = "Paypal";
        if ($recurring === true) {
            $item = [
                'name' => 'Monthly Subscription',
                'price' => 1,
                'qty' => 1,
            ];
            $cart['items'] = [$item];
            $cart['return_url'] = url('/paypal/checkout-success?mode=recurring');
            $cart['subscription_desc'] = 'Monthly Subscription';
        } else {
            $cart['items'] = [];
            $item = [
                'name' => 'Product 1',
                'price' => 1,
                'qty' => 1,
            ];
            array_push($cart['items'], $item);
            $cart['return_url'] = url('/paypal/checkout-success');
        }
        $cart['cancel_url'] = url('/');
        $total = 0;
        foreach ($cart['items'] as $item) {
            $total += $item['price'] * $item['qty'];
        }
        $cart['total'] = $total;
        return $cart;
    }

    protected function getCheckoutData($invoice)
    {
        $cart = [];
        $cart['invoice_id'] = $invoice['invoice_id'];
        $cart['invoice_description'] = $invoice['title'];
        $cart['items'] = Item::where('invoice_id', $invoice['id'])->get();
        $total = 0;
        foreach ($cart['items'] as $item) {
            $total += $item['price'] * $item['qty'];
        }
        $cart['total'] = $total;
        return $cart;
    }
    /**
     * Crear factura.
     *
     * @param array  $cart
     * @param string $status
     *
     * @return \App\Invoice
     */
    protected function createInvoice($cart, $status)
    {
        $invoice = Invoice::create([
            'title' => $cart['invoice_description'],
            'price' => $cart['total'],
            'paid' => (!strcasecmp($status, 'Completed') || !strcasecmp($status, 'Processed')) ? 1 : 0,
        ]);
        collect($cart['items'])->each(function ($product) use ($invoice) {
            Item::create([
                'invoice_id' => $invoice['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'qty' => $product['qty'],
            ]);
        });
        return $invoice;
    }

    protected function updateInvoice($invoice, $status) {
        $invoice['paid'] = (!strcasecmp($status, 'Completed') || !strcasecmp($status, 'Processed')) ? 1 : 0;
        $invoice->save();
        return $invoice;
    }
}