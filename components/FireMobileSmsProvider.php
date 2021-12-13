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
     * @var array Mandatory - Send Pwd
     */
    public $send_pwd;

    /**
     * @var string Mandatory - sender identifier
     */
    public $from = '';

    public function sendSms($sender_name,$dial_code, $phone, $message) {
        if (empty($this->send_id) || empty($this->send_pwd)) {
            throw new \Exception('SmsProvider mandatory configuration not filled in');
        }
        $mobile_no = $dial_code . $phone;
        $post_array = array(
            'gw-username' => $this->send_id,
            'gw-password' => $this->send_pwd,
            'gw-from' => $this->from, //Need firemobile to whitelist the name, so we cannot just use sender_name dynamically
            'gw-to' => $mobile_no,
            'gw-text' => $message
        );
        
        $contact = array('country_no' => $dial_code, 'contact_no' => $phone);
        $this->prev_request = json_encode(array_merge($post_array, $contact));
        $this->api_name = 'sendSms';
        try {
            $result = $this->post($this->send_url, $post_array, [
                //Do not verify host for now
                'verify' => false,
            ]);
            $this->prev_response = json_encode($result);
        } catch (\Exception $ex) {
            $this->prev_response = json_encode(['code' => $ex->getCode(), 'message' => $ex->getMessage()]);
            return false;
        }

        return true;
    }
}
