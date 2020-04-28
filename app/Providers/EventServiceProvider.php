<?php

namespace App\Providers;

use App\Events\EmailableAdded;
use App\Listeners\ReceiveSNSbKash;
use App\Listeners\SendPaymentEventEmail;
use App\Listeners\SubscribeSNSbKash;
use App\Mail\SendPaymentEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use JoggApp\AwsSns\Events\SnsMessageReceived;
use JoggApp\AwsSns\Events\SnsTopicSubscriptionConfirmed;

class EventServiceProvider extends ServiceProvider
{
    protected $subscribe = [
        // 'App\Listeners\SnsEventListenbKash'
    ];
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SnsTopicSubscriptionConfirmed::class => [
            SubscribeSNSbKash::class
        ],
        SnsMessageReceived::class => [
            ReceiveSNSbKash::class
        ],
        EmailableAdded::class => [
            SendPaymentEventEmail::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
