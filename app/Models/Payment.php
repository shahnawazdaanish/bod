<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    public function merchant() {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
