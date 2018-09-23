<?php

namespace App\Listeners;

use App\Events\SenderEvent;
use App\Mail\SenderMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SenderListener
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
     * @param  SenderEvent  $event
     * @return void
     */
    public function handle(SenderEvent $event)
    {
        if($event->data->type == 'email'){
            Mail::to($event->data->send_to)
                ->later(Carbon::now()->addSeconds(10),new SenderMail($event->data));
        }else{
            // Send SMS
            sendSMS($event->data->send_to,$event->data->body);
        }
    }
}
