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
    
    public function sendSms($sender_name,$dial_code, $phone, $message) {
        return true;
    }
}