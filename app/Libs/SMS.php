<?php

namespace App\Libs;

class SMS {

    protected $data;
    protected $url = 'https://www.smsmisr.com/api/send/?';

    public function __construct() {
        $this->data['username'] = setting('sms_username');
        $this->data['password'] = setting('sms_password');
        $this->data['sender']   = setting('sms_sender_name');
        $this->data['language'] = 2;
    }

    public function Send($mobile,$message,$delay = null) {

        /*
         * Check For delay
         * must follow format of (dd-mm-yyyy-hh-mm)
         */
        if($delay){
            if(!preg_match('/^\d{2}\-\d{2}\-\d{4}\-\d{2}\-\d{2}$/',$delay)){
                $this->data['DelayUntil'] = null;
            } else {
                $this->data['DelayUntil'] = $delay;
            }
        }

        /*
         * Check Mobile number
         */
        if(!preg_match('/^\d{11}$/',$mobile)){
            return false;
        }

        $this->data['Mobile'] = $mobile;

        /*
         * Check Message
         */
        if(!$message && !strlen($message)){
            return false;
        }
        $this->data['message'] = $message;

        $url = $this->url.http_build_query($this->data);

        $client = new \GuzzleHttp\Client;
        try {
            $response = $client->get($url);
            //response 1901, smsid: 2206223, total of numbers: 1, credit: 8379.582
            $data = explode(', ',$response->getBody());
            switch ($data['0']){
                case 1901:
                    return true;
                break;
                case 1902:
                case 1903:
                case 1904:
                case 1905:
                case 1906:
                case 1907:
                case 1908:
                    /*
                     * send it right now
                     */
                    return false;
                break;
            }
            /*
             * TODO
             * Message Sent;
             */
        } catch (RequestException $e){
            /*
             * TODO message not sent
             */
            return false;
        }

    }
    
    
    public static function GenerateMsg($vars,$message){
        array_unshift($vars,'0');
        $replaceVals = array_map(function($key){return '{'.$key.'}';},array_keys($vars));
        return str_replace($replaceVals,array_values($vars),$message);
    }


}