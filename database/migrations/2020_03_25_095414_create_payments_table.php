<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sender_account_no')->index()->comment('Payment comes from');
            $table->string('receiver_account_no')->comment('Payment goes to');
            $table->decimal('amount')->index()->comment('Paid amount');
            $table->string('trx_id')->comment('Transaction ID of this payment');
            $table->integer('merchant_id')->comment('Merchant ID')->nullable();
            $table->string('currency')->nullable();
            $table->dateTime('transaction_datetime')->nullable();
            $table->string('creditOrganizationName')->nullable();
            $table->string('transactionType')->nullable();
            $table->string('MessageId')->nullable();
            $table->string('TopicArn')->nullable();
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
        Schema::dropIfExists('payments');
    }
}
