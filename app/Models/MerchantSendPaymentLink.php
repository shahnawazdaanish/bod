<?php

namespace App\Models;

use App\User;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantSendPaymentLink extends Model
{
    use SoftDeletes;

    protected $table = 'merchant_send_pay_link';

    public function merchant(){
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }
    public function user(){
        return $this->belongsTo(Administrator::class, 'user_id', 'id');
    }
    public function payments(){
        return $this->hasMany(Payment::class, 'merchant_ref', 'merchant_invoice_no');
    }
}
