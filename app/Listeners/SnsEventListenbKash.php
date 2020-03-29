<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

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
        Log::info(json_encode($event));
    }

    public function onMessageReceived($event){
        Log::info(json_encode($event->message));
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
