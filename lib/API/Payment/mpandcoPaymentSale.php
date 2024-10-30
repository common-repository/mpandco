<?php
namespace mPandco\API\Payment;


use mPandco\API\Core\HttpConfig;
use mPandco\API\Core\HttpConnection;

/**
 * Class mpandcoPaymentSale
 * @package mPandco\API\Payment
 */
class mpandcoPaymentSale {
    /**
     *
     */
    const URL_DEFAULT_API = 'https://app.mpandco.com/';

    /**
     *
     */
    const URL_SANDBOX_API = 'https://test.mpandco.com/';
    /**
     *
     */
    const URL_API = 'api/payment-intent/.json';

    /**
     * @var
     */
    private $_params;

    /**
     * @var
     */
    private $AccessToken;

    /**
     * @var string
     */
    private $TokenType;

    /**
     * @var bool
     */
    private $test;

    /**
     * @var bool
     */
    private $unauthorized;

    /**
     * @var
     */
    private $response;

    /**
     * PaymentSale constructor.
     * @param $_params
     * @param $AccessToken
     * @param bool $test
     * @param string $TokenType
     */
    public function __construct($_params, $AccessToken, $test = false, $TokenType = 'Bearer' ) {
        $this->_params     = $_params;
        $this->AccessToken = $AccessToken;
        $this->TokenType   = $TokenType;
        $this->test = $test;
        $this->unauthorized = false;
    }

    /**
     * @return array|bool|\Exception
     */
    public function execute() {
        $response = null;
        $basepath = $this->test? self::URL_SANDBOX_API: self::URL_DEFAULT_API;
        if ($this->_params && is_array($this->_params) && $this->AccessToken && $this->TokenType){
            $payload = json_decode(json_encode($this->_params));
            $header = array('authorization' => $this->TokenType.' '.$this->AccessToken);
            $request = wp_remote_post($basepath.self::URL_API,array('body'=> $payload,'headers'=> $header));
            $this->response = json_decode(wp_remote_retrieve_body($request),true);
            if ( wp_remote_retrieve_response_code($request)==401) $this->unauthorized = true;
            if ( wp_remote_retrieve_response_code($request) < 200 &&wp_remote_retrieve_response_code($request)>=300 ) return false;
            return $this->response;
        }else{
            return false;
        }
    }


    /**
     * @return bool
     */
    public function isUnauthorized()
    {
        return $this->unauthorized;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}