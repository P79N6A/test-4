<?php

/**
 * Description of AopSdkMain
 *
 * @author drawping
 */
require_once dirname(__FILE__) . '/aop/AopClient.php';
require_once dirname(__FILE__) . '/aop/AopEncrypt.php';
require_once dirname(__FILE__) . '/aop/EncryptParseItem.php';
require_once dirname(__FILE__) . '/aop/EncryptResponseData.php';
require_once dirname(__FILE__) . '/aop/SignData.php';

class AopSdkExt extends AopClient {

    function __construct() {
        $this->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $this->appId = config('alipay.appid');
        $this->rsaPrivateKeyFilePath = dirname(__FILE__) . "/key/rsa_private_key.pem";
        $this->alipayPublicKey = dirname(__FILE__) . "/key/alipay_rsa_public_key.pem";
        $this->apiVersion = '1.0';
        $this->postCharset = 'utf-8';
        $this->format = 'json';
    }

}

class Loader {

    public static function loadClass($class) {
        require_once dirname(__FILE__).'/aop/request/'.$class.'.php';
    }

}
spl_autoload_register(array('Loader', 'loadClass'));
