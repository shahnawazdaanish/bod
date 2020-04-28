<?php

namespace App\Listeners;

use App\Events\EmailableAdded;
use App\Mail\SendPaymentEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendPaymentEventEmail
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
     * @param  EmailableAdded  $event
     * @return void
     */
    public function handle(EmailableAdded $event)
    {
        Mail::to($event->email)->send(
            new SendPaymentEmail($event->type, $event->data)
        );
    }
}
