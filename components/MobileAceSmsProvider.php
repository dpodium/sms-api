<?php

namespace dpodium\smsapi\components;

/**
 * This component is the implementation of mobile ace SMS Gateway API
 * 
 * @author Darren Ng, Dynamic Podium
 * @link http://www.dpodium.com
 * @license MIT
 */
class MobileAceSmsProvider extends \dpodium\smsapi\abstracts\SmsProvider {

    /**
     * @var string Optional - SMS Gateway URL
     */
    public $send_url = "http://210.48.155.182/bulksms/smsblast.asp";

    /**
     * @var string Optional - Message Type
     * 
     * Default: 0
     */
    public $msg_type = "0";

    /**
     * @var array Mandatory - Send ID
     */
    public $send_id;

    /**
     * @var array Mandatory - Send Pwd
     */
    public $send_pwd;

    public function sendSms($sender_name,$dial_code, $phone, $message) {
        if (empty($this->send_id) || empty($this->send_pwd)) {
            throw new \Exception('SmsProvider mandatory configuration not filled in');
        }
        $mobile_no = $dial_code . $phone;

        $post_array = array(
            'user' => $this->send_id,
            'pass' => $this->send_pwd,
            'type' => $this->msg_type,
            'to' => $mobile_no,
            'text' => $message
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
        return true;
    }

}
