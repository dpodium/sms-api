<?php

namespace dpodium\smsapi\components;

/**
 * This main entry point for Sms sending. Usage:
 * 
 * $config = [];  //Configuration array, see below for more information
 * $countryId = '60';
 * $contactNo = '123456789';
 * 
 * $sms = new \dpodium\smsapi\components\SmsManager();
 * $sms->config = $config;
 * $sms->test_mode = false;
 * $sms->setPhone($countryId, $contactNo);
 * $sms->sendSms('Hello world!');
 * 
 * @author Darren Ng, Dynamic Podium
 * @link http://www.dpodium.com
 * @license MIT
 */
class SmsManager {
    /**
     * @var array
     * Default provider classes
     */
    public $providers = [
        'cm' => CMSmsProvider::class,
        'clickatell' => ClickatellSmsProvider::class,
        'mobileace' => MobileAceSmsProvider::class,
    ];
    
    /**
     * @var string
     * Default provider to use
     */
    public $default_provider = 'cm';
    
    /**
     * @var array
     * Configuration for manager. Sample configuration:
     * 
     * [
            '*' => [  //Default configuration for all Country dial code
                'clickatell' => [  //Configuration for Clickatell
                    'api_id' => '',  //API ID
                    'send_id' => '',  //Send ID
                    'send_pwd' => '',  //Send Pwd
                ],
                'mobileace' => [  //Configuration for Mobile Ace
                    'send_id' => '',  //Send ID
                    'send_pwd' => '',  //Send Pwd
                ],
                'cm' => [  //Configuration for CM
                    'product_token' => '',  //Product token
                    'from' => '',  //Sender name
                ],
            ],
            '60' => [  //Configuration for Malaysia dial code
                'mobileace' => [],  //Use mobileace for Malaysia, default configuration
            ],
            '65' => [ //Configuration for Singapore dial code
                'clickatell' => [  //Use clickatell for Singapore, with override configuration
                    'api_id' => '',  //Override API ID, default everything else
                ],
            ],
        ]
     */
    public $config = [];
    
    /**
     * @var boolean
     * Is test mode?
     * 
     * Test mode forces the module to use a special component to mock send SMS.
     * 
     * See also \dpodium\smsapi\components\TestModeSmsProvider
     */
    public $test_mode = true;
    
    /**
     * @var array
     * Proxy setting, if behind proxy configure as array with key host and port, eg:
     * 
     * [
     *     'host' => 'my.proxy.host',
     *     'port' => 1234
     * ]
     */
    public $proxy = null;
    
    private $dial_code;
    private $phone;
    private $provider;
    
    /** @var \dpodium\smsapi\abstracts\SmsProvider */
    private $service;
    
    /**
     * @return string the provider identifier used / to be used
     */
    public function getProvider() {
        return $provider;
    }
    
    /**
     * 
     * @param string $dial_code Country dial code, eg: 60
     * @param string $phone The local number, eg: 123456789
     * @return \dpodium\smsapi\components\SmsManager this
     */
    public function setPhone($dial_code, $phone) {
        //Convert int to str
        $this->dial_code = '' . $dial_code;
        $this->phone = '' . $phone;
        
        $this->initService();
        return $this;
    }
    
    protected function initService() {
        //We normalize the config first,
        $cfg = $this->getProviderConfig();
        //And then we get the provider class,
        $clazz = $this->getProviderClass($cfg);
        //And then we create the Guzzle HTTP transport
        $this->initHttpTransport($cfg);
        //And finally we instantiate the provider class
        $this->service = new $clazz($cfg);
    }
    
    protected function getProviderConfig() {
        $this->provider = $this->default_provider;
        $cfg_providers = isset($this->config[$this->dial_code]) ? $this->config[$this->dial_code] : null;
        if (!isset($cfg_providers)) {
            $cfg_providers = $this->config['*'];
        } else {
            foreach($cfg_providers as $provider => $cfg) {
                if (isset($this->config['*'][$provider])) {
                    $cfg_providers[$provider] = $this->config['*'][$provider] + $cfg;
                }
            }
            //Use the first provider found. That is the configured provider for this dial code
            reset($cfg_providers);
            $this->provider = key($cfg_providers);
        }
        if (!isset($cfg_providers[$this->provider])) {
            throw new Exception('Configured provider not found - ' . $this->provider);
        }
        $cfg = $cfg_providers[$this->provider];
        return $cfg;
    }
    
    protected function getProviderClass(&$cfg) {
        $clazz = '';
        if (isset($cfg['class'])) {
            $clazz = $cfg['class'];
            unset($cfg['class']);
        } else if (isset($this->providers[$this->provider])) {
            $clazz = $this->providers[$this->provider];
        } else {
            throw new Exception('Provider class not configured - ' . $this->provider);
        }
        if (!class_exists($clazz)) {
            throw new Exception('Provider class not found - ' . $clazz);
        }
        //If test mode, use TestModeSmsProvider instead
        if ($this->test_mode) {
            $clazz = TestModeSmsProvider::class;
            $cfg = [];
        }
        return $clazz;
    }
    
    protected function initHttpTransport(&$cfg) {
        // If proxy configuration exists, create default Guzzle with Proxy setting
        $guzzle = new \GuzzleHttp\Client(!empty($this->proxy['host']) ? [
            'proxy' => $this->proxy['host'] . ':' . $this->proxy['port'],
        ] : []);
        $cfg['__guzzle'] = $guzzle;
    }
    
    public function sendSms($message) {
        if (!isset($this->phone)) {
            throw new Exception('Phone number not set');
        }
        $flag = $this->service->sendSms($this->dial_code, $this->phone, $message);
        return $flag;
    }
}