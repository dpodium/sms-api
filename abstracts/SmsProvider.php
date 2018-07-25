<?php

namespace dpodium\smsapi\abstracts;

/**
 * This abstract class implements the foundation of SmsProvider that \dpodium\smsapi\components\SmsManager will use
 * 
 * @author Darren Ng, Dynamic Podium
 * @link http://www.dpodium.com
 * @license MIT
 */
abstract class SmsProvider {
    
    protected $guzzle;
    
    public $api_name;
    public $prev_request;
    public $prev_response;
    
    
    public abstract function sendSms($dial_code, $phone, $message);
    
    public function __construct($config) {
        if (!isset($config['__guzzle'])) {
            $this->guzzle = new \GuzzleHttp\Client();
        } else {
            $this->guzzle = $config['__guzzle'];
            unset($config['__guzzle']);
        }
        foreach ($config as $key => $val) {
            //reflect the config into the class
            $this->$key = $val;
        }
    }
    
    protected function post($url, $data) {
        try {
            $response = $this->guzzle->post($url, ['form_params' => $data]);
        } catch (\GuzzleHttp\Exception\ClientException $ex) {
            $response = $ex->getResponse();
        }
        return $this->parseResponse($response);
    }
    
    protected function postBody($url, $data) {
        try {
            $response = $this->guzzle->post($url, ['body' => $data]);
        } catch (\GuzzleHttp\Exception\ClientException $ex) {
            $response = $ex->getResponse();
        }
        return $this->parseResponse($response);
    }
    
    protected function get($url, $data) {
        try {
            $response = $this->guzzle->get($url, ['query' => $data]);
        } catch (\GuzzleHttp\Exception\ClientException $ex) {
            $response = $ex->getResponse();
        }
        return $this->parseResponse($response);
    }
    
    protected function parseResponse($response) {
        return [
            'status' => $response->getStatusCode(),
            'headers' => $response->getHeaders(),
            'body' => $response->getBody()->getContents(),
        ];
    }
}