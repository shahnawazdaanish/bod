<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\bKashController;
use App\Models\MerchantSendPaymentLink;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class PaymentProcessorController extends Controller
{
    public function payLinkPayment(Request $request, $reference_id) {
        $linkPayment = MerchantSendPaymentLink::where('reference_id', $reference_id)
            ->first();
        return view('payment.link_pay', compact('linkPayment'));
    }
    public function payLinkCreate(Request $request, $reference_id) {
        /*$ts = '2020-04-26T04:51:09:651 GMT+0000';
        $ts = str_replace(" GMT+0000", "", $ts);
        $ts = str_replace('T', ' ', $ts);
        $ts = substr($ts, 0,-4);

        return Carbon::createFromFormat("Y-m-d H:i:s", $ts)->setTimezone('Asia/Dhaka')->toDateTimeString();
//        return Carbon::createFromFormat('Y-m-d H:i:s:SSS', $ts)->setTimezone('Asia/Dhaka')->toDateTimeString();*/
        $linkPayment = MerchantSendPaymentLink::where('reference_id', $reference_id)
            ->where('status', '!=', 'PAID')
            ->first();
        if($linkPayment) {
            $merchant = $linkPayment->merchant;
            if($merchant) {
                $bKash = new bKashController($merchant);

                $amount = $linkPayment->allow_custom_amount ? $request->get('amount') : $linkPayment->payable_amount;
                $createResponse = $bKash->createPayment(
                    $amount,
                    'BDT',
                    'sale',
                    $linkPayment->merchant_invoice_no
                );
                if(is_array($createResponse) && isset($createResponse['paymentID'])) {
                    return Response::json($createResponse, 200);
                } else {
                    return Response::json($createResponse, 400);
                }
            } else {
                return Response::json(['error' => 'Merchant information not found'], 400);
            }
        } else {
            return Response::json(['error' => 'invoice not found'], 400);
        }

    }
    public function payLinkExecute(Request $request, $reference_id){
         try {
            $paymentID = $request->get('paymentID');
            $linkPayment = MerchantSendPaymentLink::where('reference_id', $reference_id)
                ->where('status', '!=', 'PAID')
                ->first();
            if ($linkPayment) {
                $merchant = $linkPayment->merchant;
                if ($merchant) {
                    if ($paymentID) {
                        $bKash = new bKashController($merchant);
                        $executeResponse = $bKash->executePayment($paymentID);
                        if (is_array($executeResponse) && isset($executeResponse['trxID'])) {

                            // Use transaction of database to store both payment and status update along with
                            DB::beginTransaction();

                            $ts = $executeResponse['updateTime'] ?? '';
                            $ts = str_replace(" GMT+0000", "", $ts);
                            $ts = str_replace('T', ' ', $ts);
                            $ts = substr($ts, 0,-4);

                            // Payment complete
                            $payment = new Payment();
                            $payment->sender_account_no = $executeResponse['customerMsisdn'] ?? '';
                            $payment->receiver_account_no = $merchant->account_no;
                            $payment->amount = (double) $executeResponse['amount'] ?? 0.00;
                            $payment->trx_id = $executeResponse['trxID'] ?? '';
                            $payment->merchant_id = $merchant->id;
                            $payment->merchant_ref = $executeResponse['merchantInvoiceNumber'] ?? '';
                            $payment->currency = $executeResponse['currency'] ?? '';
                            $payment->transaction_datetime = isset($executeResponse['updateTime']) ?
                                Carbon::createFromFormat('Y-m-d H:i:s', $ts)->setTimezone('Asia/Dhaka')->toDateTimeString() : '';
                            $payment->transactionType = $executeResponse['transactionType'] ?? '';
                            $payment->save();
                            if ($payment) {
                                $linkPayment->status = 'PAID';
                                $linkPayment->save();

                                DB::commit();

                                return Response::json("Payment made successfully", 200);
                            } else {
                                return Response::json(['error' => 'Issue with storing your payment'], 400);
                            }
                        } else {
                            if(isset($executeResponse['errorCode'])) {
                                return Response::json($executeResponse['errorMessage'], 300);
                            } else {
                                return Response::json($executeResponse, 400);
                            }
                        }
                    } else {
                        return Response::json(['error' => 'No payment ID provided'], 400);
                    }
                } else {
                    return Response::json(['error' => 'Merchant information not found'], 400);
                }
            } else {
                return Response::json(['error' => 'invoice not found'], 400);
            }
        }
        catch (\Exception $exception){
            DB::rollBack();
            return Response::json(['error' => $exception->getMessage()], 500);
//            return Response::json(['error' => 'Unable to process request right now'], 500);
        }
    }

}
