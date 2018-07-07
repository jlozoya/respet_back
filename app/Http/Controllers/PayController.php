<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\CoursePrice;
use App\Models\UserCoursePayment;

use Openpay;
use Illuminate\Http\Request;

class PayController extends BaseController
{
    /**
     * Llama la información de lo que el usuario está comprando.
     * 
     * @see https://github.com/open-pay/openpay-php
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function createPay(Request $request) {
        $this->validate($request, [
            'course_price_id' => 'required',
            'device_session_id' => 'required',
            'token_id' => 'required',
        ]);
        try {
            Openpay::setId(env('OPENPAY_ID'));
            Openpay::setApiKey(env('OPENPAY_API_KEY'));

            $openpay = Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_API_KEY'));

            $user = $request->user();
            $customer = [
                'name' => $user['name'],
                'last_name' => $user['last_name'],
                'phone_number' => $user['phone_number'],
                'email' => $user['email'],
            ];

            $coursePrice = CoursePrice::select(
                'course_prices.id',
                'course_prices.amount',
                'courses.description'
            )->where('course_prices.id', $request->get('course_price_id'))
            ->join('courses', 'courses.id', 'course_prices.course_id')->first();

            $chargeData = [
                'amount' => $coursePrice['amount'],
                'description' => $coursePrice['description'],
                'method' => 'card',
                'source_id' => $request->get('token_id'),
                'device_session_id' => $request->get('device_session_id'),
                'customer' => $customer
            ];

            $charge = $openpay->charges->create($chargeData);

            UserCoursePayment::create([
                'user_id' => $user['id'],
                'course_price_id' => $coursePrice['id'],
                'amount' => $coursePrice['amount'],
                'description' => $coursePrice['description'],
                'charge_id' => $charge->id,
                'status' => $charge->status,
            ]);
            
            $formattedCharge = [
                'id' => $charge->id,
                'amount' => $charge->amount,
                'authorization' => $charge->authorization,
                'method' => $charge->method,
                'operation_type' => $charge->operation_type,
                'transaction_type' => $charge->transaction_type,
                'card' => [
                    'id' => $charge->card->id,
                    'type' => $charge->card->type,
                    'brand' => $charge->card->brand,
                    'address' => $charge->card->address,
                    'card_number' => $charge->card->card_number,
                    'holder_name' => $charge->card->holder_name,
                    'expiration_year' => $charge->card->expiration_year,
                    'expiration_month' => $charge->card->expiration_month,
                    'allows_charges' => $charge->card->allows_charges,
                    'allows_payouts' => $charge->card->allows_payouts,
                    'creation_date' => $charge->card->creation_date,
                    'bank_name' => $charge->card->bank_name,
                    'bank_code' => $charge->card->bank_code
                ],
                'status' => $charge->status,
                'currency' => $charge->currency,
                'exchange_rate' => [
                    'from' => $charge->exchange_rate->from,
                    'date' => $charge->exchange_rate->date,
                    'value' => $charge->exchange_rate->value,
                    'to' => $charge->exchange_rate->to
                ],
                'creation_date' => $charge->creation_date,
                'operation_date' => $charge->operation_date,
                'description' => $charge->description,
                'error_message' => $charge->error_message,
                'order_id' => $charge->order_id
            ];

            return response()->json($formattedCharge, 202);
        } catch (OpenpayApiTransactionError $e) {
            return response()->json([
                'error' => 'SERVER.TRANSACTION_ERROR',
                'message' => $e->getMessage(),
                'code' => $e->getErrorCode(),
                'category' => $e->getCategory(),
                'request_id' => $e->getRequestId(),
            ], $e->getHttpCode());
        } catch (OpenpayApiRequestError $e) {
            return response()->json([
                'error' => 'SERVER.REQUEST_ERROR',
                'message' => $e->getMessage(),
                'code' => $e->getErrorCode(),
                'category' => $e->getCategory(),
                'request_id' => $e->getRequestId(),
            ], $e->getHttpCode());
        } catch (OpenpayApiConnectionError $e) {
            return response()->json([
                'error' => 'SERVER.CONNECTION_ERROR',
                'message' => $e->getMessage(),
                'code' => $e->getErrorCode(),
                'category' => $e->getCategory(),
                'request_id' => $e->getRequestId(),
            ], $e->getHttpCode());
        } catch (OpenpayApiAuthError $e) {
            return response()->json([
                'error' => 'SERVER.AUTH_ERROR',
                'message' => $e->getMessage(),
                'code' => $e->getErrorCode(),
                'category' => $e->getCategory(),
                'request_id' => $e->getRequestId(),
            ], $e->getHttpCode());
        } catch (OpenpayApiError $e) {
            return response()->json([
                'error' => 'SERVER.API_ERROR',
                'message' => $e->getMessage(),
                'code' => $e->getErrorCode(),
                'category' => $e->getCategory(),
                'request_id' => $e->getRequestId(),
            ], $e->getHttpCode());
        } catch (Exception $e) {
            return response()->json([
                'error' => 'SERVER.EXCEPTION_ERROR',
                'message' => $e->getMessage(),
                'code' => $e->getErrorCode(),
                'category' => $e->getCategory(),
                'request_id' => $e->getRequestId(),
            ], $e->getHttpCode());
        }
    }

    /**
     * Llama los cargos realizados
     * 
     * @see https://github.com/open-pay/openpay-php
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function getListOfCharges(Request $request) {
        $this->validate($request, [
            'creation_start' => 'required|date',
            'creation_end' => 'required|date',
            'offset' => 'required|numeric',
            'limit' => 'required|numeric',
        ]);
        try {
            $openpay = Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_API_KEY'));

            $findData = [
                'creation[gte]' => $request->get('creation_start'),
                'creation[lte]' => $request->get('creation_end'),
                'offset' => $request->get('offset'),
                'limit' => $request->get('limit')
            ];
            
            $chargeList = $openpay->charges->getList($findData);

            foreach ($chargeList as &$charge) {
                $charges[] = [
                    'id' =>  $charge->id,
                    'amount' => $charge->amount,
                    'authorization' => $charge->authorization,
                    'method' => $charge->method,
                    'operation_type' => $charge->operation_type,
                    'transaction_type' => $charge->transaction_type,
                    'card' => [
                        'type' => $charge->card->type,
                        'brand' => $charge->card->brand,
                        'address' => $charge->card->address,
                        'card_number' => $charge->card->card_number,
                        'holder_name' => $charge->card->holder_name,
                        'expiration_year' => $charge->card->expiration_year,
                        'expiration_month' => $charge->card->expiration_month,
                        'allows_charges' => $charge->card->allows_charges,
                        'allows_payouts' => $charge->card->allows_payouts,
                        'bank_name' => $charge->card->bank_name,
                        'bank_code' => $charge->card->bank_code
                    ],
                    'status' => $charge->status,
                    'currency' => $charge->currency,
                    'creation_date' => $charge->creation_date,
                    'operation_date' => $charge->operation_date,
                    'description' => $charge->description,
                    'error_message' => $charge->error_message,
                    'order_id' => $charge->order_id,
                    'customer_id' => $charge->customer_id
                ];
            }
            return response()->json($charges, 200);

        } catch (OpenpayApiTransactionError $e) {
            return response()->json([
                'error' => 'SERVER.TRANSACTION_ERROR',
                'message' => $e->getMessage(),
                'code' => $e->getErrorCode(),
                'category' => $e->getCategory(),
                'request_id' => $e->getRequestId(),
            ], $e->getHttpCode());
        } catch (OpenpayApiRequestError $e) {
            return response()->json([
                'error' => 'SERVER.REQUEST_ERROR',
                'message' => $e->getMessage(),
                'code' => $e->getErrorCode(),
                'category' => $e->getCategory(),
                'request_id' => $e->getRequestId(),
            ], $e->getHttpCode());
        } catch (OpenpayApiConnectionError $e) {
            return response()->json([
                'error' => 'SERVER.CONNECTION_ERROR',
                'message' => $e->getMessage(),
                'code' => $e->getErrorCode(),
                'category' => $e->getCategory(),
                'request_id' => $e->getRequestId(),
            ], $e->getHttpCode());
        } catch (OpenpayApiAuthError $e) {
            return response()->json([
                'error' => 'SERVER.AUTH_ERROR',
                'message' => $e->getMessage(),
                'code' => $e->getErrorCode(),
                'category' => $e->getCategory(),
                'request_id' => $e->getRequestId(),
            ], $e->getHttpCode());
        } catch (OpenpayApiError $e) {
            return response()->json([
                'error' => 'SERVER.API_ERROR',
                'message' => $e->getMessage(),
                'code' => $e->getErrorCode(),
                'category' => $e->getCategory(),
                'request_id' => $e->getRequestId(),
            ], $e->getHttpCode());
        } catch (Exception $e) {
            return response()->json([
                'error' => 'SERVER.EXCEPTION_ERROR',
                'message' => $e->getMessage(),
                'code' => $e->getErrorCode(),
                'category' => $e->getCategory(),
                'request_id' => $e->getRequestId(),
            ], $e->getHttpCode());
        }
    }
}
