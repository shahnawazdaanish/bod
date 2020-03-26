<?php

namespace App\Admin\Actions\MissedPayment;

use App\Admin\Controllers\bKashController;
use App\Models\MissedPayment;
use App\Models\Payment;
use Carbon\Carbon;
use Encore\Admin\Actions\RowAction;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;

class Approve extends RowAction
{
    public $name = 'Approve';

    public function dialog()
    {
        $this->confirm('Are you sure to approve this payment approval request?');
    }

    public function handle(Model $model)
    {
        $merchant = $model->merchant;

        try {
            // $model ...
            if($merchant) {
                $user = Admin::user();

                $userWithRole = Administrator::whereHas('roles',  function ($query) {
                    $query->whereIn('slug', ['payment-admin']);
                })->where('id', $user->id)->first();

                if($userWithRole) {
                    if (isset($userWithRole->merchant_id) && !empty($userWithRole->merchant_id)) {
                        if ($userWithRole->merchant_id != $merchant->id) {
                            return $this->response()->error("You do not have access to approve this");
                        }
                    }

                    if ($model->status == 'PENDING') {
                        $bkash = new bKashController($merchant);
                        $resp = $bkash->searchTransaction($model->transaction_id);

                        if (is_array($resp)) {
                            $payment = new Payment();
                            $payment->sender_account_no = isset($resp['customerMsisdn']) ? $resp['customerMsisdn'] : '';
                            $payment->receiver_account_no = isset($resp['organizationShortCode']) ? $resp['organizationShortCode'] : '';
                            $payment->amount = isset($resp['amount']) ? (double)$resp['amount'] : '';
                            $payment->trx_id = isset($resp['trxID']) ? $resp['trxID'] : '';
                            $payment->merchant_id = $merchant->id;
                            $payment->currency = isset($resp['currency']) ? $resp['currency'] : '';
                            $payment->transaction_datetime = isset($resp['completedTime']) ?
                                Carbon::createFromFormat('Y-m-d H:i:s',
                                    str_replace('T', ' ', str_replace(":000 GMT+0600", "", $resp['completedTime']))
                                )->setTimezone('Asia/Dhaka')->toDateTimeString() : '';
                            $payment->transactionType = isset($resp['transactionType']) ? $resp['transactionType'] : '';
                            $payment->save();
                            if ($payment) {
                                $model->status = "APPROVED";
                                $model->approved_by = Admin::user()->id;
                                $model->save();

                                return $this->response()->success('Payment processed successfully')->refresh();
                            }
                        } else {
                            return $this->response()->error("Payment not found");
                        }
                    } else {
                        return $this->response()->error("This payment request is already approved");
                    }
                } else {
                    return $this->response()->error("You are not allowed for this, ask you admin");
                }
            } else {
                return $this->response()->error("Merchant information not found");
            }
        } catch (\Exception $e) {
            return $this->response()->error("Exception => [". $e->getLine() . "]:" . $e->getMessage());
        }
    }

}