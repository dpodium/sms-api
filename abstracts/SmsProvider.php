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
    
    protected function post($url, $data, $guzzle_configs = []) {
        try {
            $response = $this->guzzle->post($url, array_merge(isset($guzzle_configs) ? $guzzle_configs : [], ['form_params' => $data]));
        } catch (\GuzzleHttp\Exception\ClientException $ex) {
            $response = $ex->getResponse();
        }
        return $this->parseResponse($response);
    }
    
    protected function postBody($url, $data, $guzzle_configs = []) {
        try {
            $response = $this->guzzle->post($url, array_merge(isset($guzzle_configs) ? $guzzle_configs : [], ['body' => $data]));
        } catch (\GuzzleHttp\Exception\ClientException $ex) {
            $response = $ex->getResponse();
        }
        return $this->parseResponse($response);
    }
    
    protected function get($url, $data, $guzzle_configs = []) {
        try {
            $response = $this->guzzle->get($url, array_merge(isset($guzzle_configs) ? $guzzle_configs : [], ['query' => $data]));
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