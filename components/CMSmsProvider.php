<?php

namespace dpodium\smsapi\components;

/**
 * This component is the implementation of CM SMS Gateway API
 * 
 * @author Darren Ng, Dynamic Podium
 * @link http://www.dpodium.com
 * @license MIT
 */
class CMSmsProvider extends \dpodium\smsapi\abstracts\SmsProvider {

    /**
     * @var string Optional - SMS Gateway URL
     */
    public $send_url = "https://sgw01.cm.nl/gateway.ashx";
    
    /**
     * @var string Mandatory - sender identifier
     */
    public $from = '';
    
    /**
     * @var boolean Optional - should use unicode?
     * 
     * Default: false
     */
    public $unicode_msg = false;
    
    /**
     * @var string Mandatory - product token
     */
    public $product_token;

    public function sendSms($dial_code, $phone, $message) {
        if (empty($this->product_token) || empty($this->from)) {
            throw new Exception('SmsProvider mandatory configuration not filled in');
        }
        $mobile_no = $dial_code . $phone;
        $xml = $this->buildMessageXml($mobile_no, $message);
        $result = $this->post($this->send_url, $xml);
        return true;
    }
    
    protected function buildMessageXml($recipient, $message) {
        if (strpos($recipient, '+') !== 0) {
            $recipient = '+' . $recipient;
        }
        $xml = new \SimpleXMLElement('<MESSAGES/>');

        $authentication = $xml->addChild('AUTHENTICATION');
        $authentication->addChild('PRODUCTTOKEN', $this->product_token);

        $msg = $xml->addChild('MSG');
        $msg->addChild('FROM', $this->from);
        $msg->addChild('TO', $recipient);
        if ($this->unicode_msg) {
            $msg->addChild('DCS', 8);
        }
        $len = mb_strlen($message);
        if (!$this->unicode_msg && $len >= 160) {
            $msg->addChild('MINIMUMNUMBEROFMESSAGEPARTS', 1);
            $msg->addChild('MAXIMUMNUMBEROFMESSAGEPARTS', ceil($len / 153));
        } else if ($this->unicode_msg && $len >= 70) {
            $msg->addChild('MINIMUMNUMBEROFMESSAGEPARTS', 1);
            $msg->addChild('MAXIMUMNUMBEROFMESSAGEPARTS', ceil($len / 70));
        }
        $msg->addChild('BODY', $message);

        return $xml->asXML();
    }
}
