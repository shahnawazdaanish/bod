<?php

use Illuminate\Routing\Router;
Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->resource('templates', 'TemplateController');
    $router->resource('merchants', 'MerchantController');
    $router->resource('all_payments', 'PaymentController');
    $router->resource('missed_payments', 'MissedPaymentController');
    $router->resource('send-payment-notification', 'SendPaymentNotificationController');
    $router->resource('send-payment-link', 'SendPaymentLinkController');
    $router->resource('users', 'UserController');

    $router->get('search_payment', 'SearchPaymentController@searchPayment');
    $router->get('search_payment/{trxid}', 'SearchPaymentController@searchPayment')->name('search_payment');
    $router->post('search_submit', 'SearchPaymentController@searchSubmit')->name('search_submit');
    $router->post('payment_used', 'SearchPaymentController@markPaymentUsed')->name('payment_used');

});
