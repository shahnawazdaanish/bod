<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MissedPayment extends Model
{
    use SoftDeletes;

    protected $table = 'missed_payments';

    public function merchant(){
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }
}
