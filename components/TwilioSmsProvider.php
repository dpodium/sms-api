<?php

namespace dpodium\smsapi\components;

/**
 * This component is the implementation of twilio SMS Gateway API
 * 
 * @author Darren Ng, Dynamic Podium
 * @link http://www.dpodium.com
 * @license MIT
 */
class TwilioSmsProvider extends \dpodium\smsapi\abstracts\SmsProvider {
    
    /**
     * @var string Mandatory - SID
     */
    public $sid;
    
    /**
     * @var array Mandatory - Token
     */
    public $token;
    
    /**
     * @var array Mandatory - A Twilio phone number you purchased at twilio.com/console
     */
    public $sender_num;
    
    public function sendSms($sender_name,$dial_code, $phone, $message) {
        if (empty($this->sid) || empty($this->token) || empty($this->sender_num)) {
            throw new Exception('SmsProvider mandatory configuration not filled in');
        }
        $mobile_no = $dial_code . $phone;
        $client = new \Twilio\Rest\Client($this->sid, $this->token);

        $params = [
            'from' => $this->sender_num,
            'body' => $message
        ];
        $this->prev_request = json_encode(array_merge([
            'mobile_no' => $mobile_no,
        ], $params));
        $this->api_name = 'sendSms';
        
        $client->messages->create($mobile_no, $params);
        $this->prev_response = json_encode([]);
        return true;
    }

}