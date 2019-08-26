<?php

namespace App\Http\Controllers\Store;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\PayPal\Invoice;
use App\Models\PayPal\IPNStatus;
use App\Models\PayPal\Item;
use App\Models\Store\Warehouse;
use App\Models\Store\Order;
use App\Models\Store\Product;
use App\Models\Store\OrderProduct;
use App\Models\Store\ProductMedia;
use App\Models\Generic\Media;
use App\Models\Generic\Direction;
use App\Models\User\User;
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

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getExpressCheckoutByOrderId(Request $request, $id)
    {
        $recurring = ($request->get('mode') === 'recurring') ? true : false;
        $cart = $this->getCartData($id, $recurring);
        if ($cart) {
            try {
                $response = $this->provider->setExpressCheckout($cart, $recurring);
                if ($response['paypal_link']) {
                    return response()->json($response['paypal_link'], 200);
                } else {
                    return response()->json('PAYPAL_ERROR', 500);
                }
            } catch (\Exception $exception) {
                $invoice = $this->updateInvoice($invoice, 'Invalid');
                return response()->json($exception->getMessage(), 400);
            }
        } else {
            return response()->json('CART_NOT_FOUND', 404);
        }
    }
    
    /**
     * Configure los datos del carrito para procesar el pago en PayPal.
     *
     * @param bool $recurring
     *
     * @return array
     */
    protected function getCartData($id, $recurring = false)
    {
        $order = Order::find($id);
        if ($order) {
            $order = $this->fillOrderData($order);
            $cart = [];
            $cart['invoice_description'] = "Paypal";
            $invoice = $this->createInvoice($cart, 'Registered');
            $cart['invoice_id'] = $invoice['id'];
            if ($recurring === true) {
                $cart['items'] = [];
                foreach ($order['products'] as &$orderProduct) {
                    $item = Item::create([
                        'invoice_id' => $cart['invoice_id'],
                        'name' => $orderProduct['product']['name'],
                        'price' => $orderProduct['product']['price'],
                        'qty' => $orderProduct['amount'],
                    ]);
                    array_push($cart['items'], $item);
                }
                $cart['return_url'] = url('/paypal/checkout-success?mode=recurring');
                $cart['subscription_desc'] = 'Monthly Subscription';
            } else {
                $cart['items'] = [];
                foreach ($order['products'] as &$orderProduct) {
                    $item = Item::create([
                        'invoice_id' => $cart['invoice_id'],
                        'name' => $orderProduct['product']['name'],
                        'price' => $orderProduct['product']['price'],
                        'qty' => $orderProduct['amount'],
                    ]);
                    array_push($cart['items'], $item);
                }
                $cart['return_url'] = url(env('PAYPAL_RETURN_URL', '/'));
            }
            $cart['cancel_url'] = url(env('PAYPAL_CANCEL_URL', '/'));
            $total = 0;
            foreach ($cart['items'] as $item) {
                $total += $item['price'] * $item['qty'];
            }
            $cart['total'] = $total;
            $invoice['total'] = $total;
            $invoice->save();
            return $cart;
        } else {
            return null;
        }
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
        $order['products'] = OrderProduct::where('order_id', $order['id'])->get();
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
            'total' => 0,
            'paid' => (!strcasecmp($status, 'Completed') || !strcasecmp($status, 'Processed')) ? 1 : 0,
        ]);
        return $invoice;
    }
    
    protected function updateInvoice($invoice, $status) {
        $invoice['paid'] = (!strcasecmp($status, 'Completed') || !strcasecmp($status, 'Processed')) ? 1 : 0;
        $invoice->save();
        return $invoice;
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
            // (Optional) Describe quién paga las tarifas de PayPal.
            // Los valores permitidos son: 'SENDER', 'PRIMARYRECEIVER', 'EACHRECEIVER' (Predeterminado), 'SECONDARYONLY'
            'payer'      => 'EACHRECEIVER',
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
}