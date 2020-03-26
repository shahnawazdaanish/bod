<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMissedPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('missed_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('msisdn')->index()->comment('Customer bKash wallet number')->nullable();
            $table->string('transaction_id')->comment('Payment transaction ID');
            $table->bigInteger('merchant_id')->comment('Merchant ID tagging')->nullable();
            $table->string('status')->default('PENDING')->comment('PENDING, SOLVED');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('missed_payments');
    }
}
