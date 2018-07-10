<?php

namespace dpodium\smsapi\components;

class FireMobileSmsProvider extends \dpodium\smsapi\abstracts\SmsProvider {

    /**
     * @var string Optional - SMS Gateway URL
     */
    public $send_url = "https://110.4.44.41:15001/cgi-bin/sendsms";

    /**
     * @var array Mandatory - Send ID
     */
    public $send_id;

    /**
     * @var string Mandatory - sender identifier
     */
    public $from = '';

    /**
     * @var array Mandatory - Send Pwd
     */
    public $send_pwd;

    public function sendSms($dial_code, $phone, $message) {
        if (empty($this->send_id) || empty($this->send_pwd)) {
            throw new Exception('SmsProvider mandatory configuration not filled in');
        }
        $mobile_no = $dial_code . $phone;
        $post_array = array(
            'gw-username' => $this->send_id,
            'gw-password' => $this->send_pwd,
            'gw-from' => $this->from,
            'gw-to' => $mobile_no,
            'gw-text' => $message
        );

        $result = $this->get($this->send_url, $post_array);
        return true;
    }

    

}
