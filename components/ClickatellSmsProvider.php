<?php

namespace dpodium\smsapi\components;

/**
 * This component is the implementation of clickatell SMS Gateway API
 * 
 * @author Darren Ng, Dynamic Podium
 * @link http://www.dpodium.com
 * @license MIT
 */
class ClickatellSmsProvider extends \dpodium\smsapi\abstracts\SmsProvider {
    
    /**
     * @var string Optional - SMS Gateway URL
     */
    public $send_url = "http://api.clickatell.com/http/sendmsg";
    
    /**
     * @var boolean Optional - should use unicode?
     * 
     * Default: false
     */
    public $unicode_msg = false;
    
    /**
     * @var array Optional - Extra data to send over to clickatell
     * 
     * Default: null
     */
    public $extra = null;
    
    /**
     * @var array Mandatory - API ID
     */
    public $api_id;
    
    /**
     * @var array Mandatory - Send ID
     */
    public $send_id;
    
    /**
     * @var array Mandatory - Send Pwd
     */
    public $send_pwd;

    public function sendSms($dial_code, $phone, $message) {
        if (empty($this->api_id) || empty($this->send_id) || empty($this->send_pwd)) {
            throw new Exception('SmsProvider mandatory configuration not filled in');
        }
        $mobile_no = $dial_code . $phone;
        
        $post_array = array('api_id' => $this->api_id,
            'user' => $this->send_id,
            'password' => $this->send_pwd,
            'text' => $message,
            'to' => $mobile_no
        );

        if (isset($this->extra) && is_array($this->extra)) {
            $post_array = array_merge($post_array, $this->extra);
        }

        if ($this->unicode_msg === true) {
            $post_array['unicode'] = '1';
        }

        $result = $this->post($this->send_url, $post_array);
        return true;
    }

}