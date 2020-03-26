<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SnsEventListenbKash
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //
    }

    public function onSubscriptionRequest($event) {
        dd($event);
    }

    public function onMessageReceived($event){
        dd($event);
    }

    public function subscribe($events)
    {
        $events->listen(
            'JoggApp\AwsSns\Events\SnsTopicSubscriptionConfirmed',
            'App\Listeners\SnsEventListenbKash@onSubscriptionRequest'
        );

        $events->listen(
            'JoggApp\AwsSns\Events\SnsMessageReceived',
            'App\Listeners\SnsEventListenbKash@onMessageReceived'
        );
    }
}
