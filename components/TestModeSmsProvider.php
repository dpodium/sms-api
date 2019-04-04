<?php

namespace dpodium\smsapi\components;

/**
 * This component is the mock implementation for testing purpose
 * 
 * @author Darren Ng, Dynamic Podium
 * @link http://www.dpodium.com
 * @license MIT
 */
class TestModeSmsProvider extends \dpodium\smsapi\abstracts\SmsProvider {
    
    public function sendSms($sender_name, $dial_code, $phone, $message) {
        $this->prev_request = json_encode([
            'sender_name' => $sender_name,
            'dial_code' => $dial_code,
            'phone' => $phone,
            'message' => $message,
        ]);
        $this->api_name = 'test';
        $this->prev_response = 'test';
        return true;
    }
}