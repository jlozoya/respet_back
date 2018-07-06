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
                'method' => 'card',
                'authorization' => $charge['authorization'],
                'creation_date' => $charge['creation_date'],
                'status' => $charge['status'],
            ]);
            return response()->json([
                'authorization' => $charge['authorization'],
                'creation_date' => $charge['creation_date'],
                'currency' => $charge['currency'],
                'customer_id' => $charge['customer_id'],
                'operation_type' => $charge['operation_type'],
                'status' => $charge['status'],
                'transaction_type' => $charge['transaction_type'],
            ], 202);
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
