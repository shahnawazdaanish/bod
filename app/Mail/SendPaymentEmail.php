<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPaymentEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $type;
    public $data;

    /**
     * Create a new message instance.
     *
     * @param $data
     * @param $type
     * @return void
     */
    public function __construct(string $type, Model $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->type == 'payment_link') {

            $data = [
                'payable_amount' => $this->data->payable_amount,
                'reference_id' => $this->data->reference_id,
                'merchant_name' => $this->data->merchant->name,
                'payable_product_description' => $this->data->payable_product_description,
            ];
            return $this->subject('Payment Service - bKash')->view('emails.send_payment_link_email', $data);
        } else if($this->type == 'payment_notification') {
            $data = [
                'amount' => $this->data->amount,
                'reference_id' => $this->data->reference_id,
                'merchant_name' => $this->data->merchant->name,
                'content' => $this->data->content,
            ];
            return $this->subject('Payment Service - bKash')->view('emails.send_payment_notification_email', $data);
        }
    }
}
