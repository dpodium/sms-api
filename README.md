# sms-api
Multiple SMS Gateway Providers API packaged together for general SMS sending use.

Ability to configure specific provider to use on specific country dial code.

## Requirements
At least php5.5 and above is required to use this module.

## Installation
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ composer require dpodium/sms-api
```

or add

```
"dpodium/sms-api": "dev-master"
```

to the `require` section of your `composer.json` file.

## Usage
Setup component for usage like such:

```php
$config = [];  //Configuration array, see \dpodium\smsapi\components\SmsManager for more information

$sms = new \dpodium\smsapi\components\SmsManager();
$sms->config = $config;
$sms->test_mode = false;
```

Use the component like such:

```php
$countryId = '60';
$contactNo = '123456789';
$sms->setPhone($countryId, $contactNo);
$sms->sendSms('Hello world!');
```

For more information and options, see \dpodium\smsapi\components\SmsManager and the individual provider components.

## Providers
CM - https://www.smsgateway.to/en
Clickatell - https://www.clickatell.com/
Mobile Ace - N/A

## Footnotes
We are not affiliated with the SMS Gateway API Providers in any way. Usage of their services are subjected to their charges which are borne by user using the service.