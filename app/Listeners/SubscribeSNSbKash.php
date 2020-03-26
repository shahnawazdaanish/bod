<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use JoggApp\AwsSns\Events\SnsTopicSubscriptionConfirmed;

class SubscribeSNSbKash
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
     * @param  SnsTopicSubscriptionConfirmed  $event
     * @return void
     */
    public function handle(SnsTopicSubscriptionConfirmed $event)
    {
        //
        Log::info("SNS SUB: ". json_encode($event));
    }
}
