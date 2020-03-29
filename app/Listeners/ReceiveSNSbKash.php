<?php

namespace App\Listeners;

use App\Models\Merchant;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use JoggApp\AwsSns\Events\SnsMessageReceived;

class ReceiveSNSbKash
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SnsMessageReceived  $event
     * @return void
     */
    public function handle(SnsMessageReceived $event)
    {
        $data = $event->message->toArray();
        // dd($data);
        if (isset($data['Message'])) {
            $resp = json_decode($data['Message'], true);

            if (isset($resp['creditShortCode']) && !empty($resp['creditShortCode'])) {
                $merchant = Merchant::where('account_no', substr($resp['creditShortCode'], -11))
                    ->where('status', 'ACTIVE')->first();
                if ($merchant) {

                    $payment = new Payment();
                    $payment->sender_account_no = isset($resp['debitMSISDN']) ? $resp['debitMSISDN'] : '';
                    $payment->receiver_account_no = isset($resp['creditShortCode']) ? $resp['creditShortCode'] : '';
                    $payment->amount = isset($resp['amount']) ? (float) $resp['amount'] : '';
                    $payment->trx_id = isset($resp['trxID']) ? $resp['trxID'] : '';
                    $payment->merchant_id = $merchant->id;
                    $payment->currency = isset($resp['currency']) ? $resp['currency'] : '';
                    $payment->transaction_datetime = isset($resp['dateTime']) ?
                        Carbon::createFromFormat(
                            'YmdHis',
                            $resp['dateTime']
                        )->setTimezone('Asia/Dhaka')->toDateTimeString() : '';
                    $payment->transactionType = isset($resp['transactionType']) ? $resp['transactionType'] : '';
                    $payment->creditOrganizationName = isset($resp['creditOrganizationName']) ? $resp['creditOrganizationName'] : '';
                    $payment->save();
                    if ($payment) {
                    }

                    // dd($resp);
                } else {
                    // nothing to do
                }
            } else {
                // nothing to do
            }
        }
        Log::info("SNS RECEIVE: " . json_encode($event));
    }
}
