<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::awsSnsWebhooks('bkash_payment_notifications');
Route::get('/', function () {
    return redirect()->to('/admin');
});
Route::get('/sendmail', function () {
    \Illuminate\Support\Facades\Mail::send(array(), array(), function ($message) {
        $message->to('shahnawazdaanish@gmail.com')
            ->subject('test')
            ->from('sdaanish@live.com')
            ->setBody('Hi', 'text/html');
    });
});

Route::get('/v', function(){
    $data = [
        'amount' => 500,
        'reference_id' => 'XXXXXX',
        'merchant_name' => 'ACI LOGISTICS LIMITED',
        'content' => 'BROILER CHICKEN - 2KG',
    ];
   return view('emails.send_payment_link_email', $data);
});


Route::get('/pay/link/{reference_id}', 'PaymentProcessorController@payLinkPayment')->name('payment_link');
Route::get('/pay/link/create/{reference_id}', 'PaymentProcessorController@payLinkCreate')->name('payment_link_create');
Route::get('/pay/link/execute/{reference_id}', 'PaymentProcessorController@payLinkExecute')->name('payment_link_execute');
