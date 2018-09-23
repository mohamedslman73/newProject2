<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SenderMail extends Mailable
{
    use Queueable, SerializesModels;
    private $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */


    public function build()
    {
        $subject = $this->data->subject;
        $this->subject($subject);
        $this->from($this->data->from_email,$this->data->from_name);

        if($this->data->file){
            $this->attach($this->data->file);
        }
//
//        $this->markdown('mail.html.message');
//        $this->markdown('mail.html.button');
//
//        $this->view('mail.sender')
//            ->with('data',$this->data)
//            ->with('url','http://www.egpay.com')
//            ->withSlot('AMRALAA');

        $this->withSwiftMessage(function ($message) use($subject) {
            $message->getHeaders()->addTextHeader('subject', $subject);
            $message->getHeaders()->addTextHeader('campaign_id', '1');
        });

        return $this->markdown('emails.orders.shipped',['data'=>$this->data]);


    }





}
