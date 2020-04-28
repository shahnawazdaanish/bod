<?php

namespace App\Models;

use App\User;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantSendPaymentNotification extends Model
{
    use SoftDeletes;

    protected $table = 'merchant_send_pay_notification';

    public function merchant(){
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }
    public function user(){
        return $this->belongsTo(Administrator::class, 'user_id', 'id');
    }
}
