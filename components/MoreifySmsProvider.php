<?php

namespace dpodium\smsapi\components;

/**
 * This component is the implementation of moreify SMS Gateway API
 * 
 * @author Darren Ng, Dynamic Podium
 * @link http://www.dpodium.com
 * @license MIT
 */
class MoreifySmsProvider extends \dpodium\smsapi\abstracts\SmsProvider {
    
    /**
     * @var string Optional - SMS Gateway URL
     */
    public $send_url = 'https://mapi.moreify.com/api/v1/sendSms';
    
    /**
     * @var string Mandatory - Project
     */
    public $project;
    
    /**
     * @var array Mandatory - Password
     */
    public $password;
    
    public function sendSms($sender_name,$dial_code, $phone, $message) {
        if (empty($this->project) || empty($this->password)) {
            throw new \Exception('SmsProvider mandatory configuration not filled in');
        }
        $mobile_no = $dial_code . $phone;
        
        $post_array = array(
            'project' => $this->project,
            'password' => $this->password,
            'phonenumber' => $mobile_no,
            'message' => $message,
        );
        
        $contact = array('country_no' => $dial_code, 'contact_no' => $phone);
        $this->prev_request = json_encode(array_merge($post_array, $contact));
        $this->api_name = 'sendSms';
        try {
            $result = $this->post($this->send_url, $post_array);
            $this->prev_response = json_encode($result);
        } catch (\Exception $ex) {
            $this->prev_response = json_encode(['code' => $ex->getCode(), 'message' => $ex->getMessage()]);
        }

//        $result = $this->post($this->send_url, $post_array);
        $json = !empty($result['body']) ? json_decode($result['body'], true) : [];
        if (isset($json) && isset($json['success']) && $json['success']) {
            return true;
        } else {
            throw new \Exception('Moreify failed to send sms: ' . json_encode($result));
        }
        return ;
    }

}