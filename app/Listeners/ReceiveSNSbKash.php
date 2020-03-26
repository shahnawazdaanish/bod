<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use JoggApp\AwsSns\Events\SnsMessageReceived;

class ReceiveSNSbKash
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
     * @param  SnsMessageReceived  $event
     * @return void
     */
    public function handle(SnsMessageReceived $event)
    {
        Log::info("SNS RECEIVE: ". json_encode($event));
    }
}
