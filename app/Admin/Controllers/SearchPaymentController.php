<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Encore\Admin\Actions\Toastr;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class SearchPaymentController extends Controller
{
    public function searchPayment(Content $content, $trxid = null)
    {
        $payment = null;
        if($trxid) {
            $payment = Payment::where('trx_id', $trxid);

            $user = Admin::user();
            if (isset($user->merchant_id) && !empty($user->merchant_id)) {
                $payment = $payment->where('merchant_id', $user->merchant_id);
            }
            $payment = $payment->first();
        }
        return $content
            ->title('Search Payment')
            ->description('Query about payment if it exists or not')
            ->view('search_box', ['payment' => $payment]);
    }

    public function searchSubmit(Content $content)
    {
        $toast = new Toastr();
        $validator = Validator::make(request()->all(), [
            'trxid' => 'required'
        ]);
        if ($validator->fails()) {
            admin_toastr($validator->errors()->first(), 'error');
            return redirect()->back();
        }

        $payment = Payment::where('trx_id', request()->get('trxid'));

        $user = Admin::user();
        if (isset($user->merchant_id) && !empty($user->merchant_id)) {
            $payment = $payment->where('merchant_id', $user->merchant_id);
        }
        $payment = $payment->first();
        if ($payment) {
            return redirect()->route('search_payment', ['trxid' => $payment->trx_id])->withInput();
//            return $content
//                ->title('Search Payment')
//                ->description('Query about payment if it exists or not')
//                ->view('search_box', ['payment'=>$payment, 'trxid' => request()->get('trxid')]);
        } else {
            admin_toastr("Payment not found", 'error');
            return redirect()->back();
        }

    }
    public function markPaymentUsed(Content $content)
    {
        $toast = new Toastr();
        $validator = Validator::make(request()->all(), [
            'payment_id' => 'required'
        ]);
        if ($validator->fails()) {
            admin_toastr($validator->errors()->first(), 'error');
            return redirect()->back();
        }
        try {
            $id = Crypt::decrypt(request()->get('payment_id'));
            $payment = Payment::where('id', $id);

            $user = Admin::user();
            if (isset($user->merchant_id) && !empty($user->merchant_id)) {
                $payment = $payment->where('merchant_id', $user->merchant_id);
            }
            $payment = $payment->first();
            if ($payment) {
                $payment->payment_status = 1;
                $payment->used_by = $user->id;
                $payment->save();
                return redirect()->route('search_payment', ['trxid' => $payment->trx_id])->withInput();
            } else {
                admin_toastr("Payment not found", 'error');
                return redirect()->back();
            }
        }
        catch (\Exception $e){
            admin_toastr("Error: ".$e->getMessage(), 'error');
            return redirect()->back();
        }
    }
}
