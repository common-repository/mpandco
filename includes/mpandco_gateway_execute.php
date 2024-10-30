<?php

use mPandco\API\Payment\mpandcoPaymentExecute;
use mPandco\API\Models\mpandcoPaymentExecution;

/**
 * Class mpandco_gateway_execute
 */
class mpandco_gateway_execute
{
    /**
     * @var mixed
     */
    private $intent;

    /**
     * @var mixed
     */
    private $payerID;

    /**
     * @var mpandco_gateway
     */
    private $gateway;

    /**
     * @var mixed
     */
    private $accesstoken;

    /**
     * @var bool
     */
    private  $test;

    /**
     * @var bool
     */
    private $unauthorized;

    /**
     * WC_Gateway_mPandco_Execute constructor.
     * @param $gateway
     * @param $accesstoken
     * @param bool $test
     */
    public function __construct($gateway, $accesstoken, $test = false) {
        $this->gateway = $gateway;
        $this->accesstoken = $accesstoken;
        $this->test = $test;
    }


    /**
     * @return array|bool|Exception
     */
    public function mpandco_execute(){
        $PaymentExecutionModel = new mpandcoPaymentExecution();
        $PaymentExecutionModel->setPaymentIntent($this->intent);
        $PaymentExecutionModel->setPayer($this->payerID);
        $datarequest = $PaymentExecutionModel->jsonSerialize();
        $PaymentExecute = new mpandcoPaymentExecute($datarequest,$this->accesstoken,$this->test);
        $request = $PaymentExecute->execute();
        $this->unauthorized = $PaymentExecute->isUnauthorized();
        return $request;
    }


    /**
     * @return mixed
     */
    public function getIntent() {
        return $this->intent;
    }


    /**
     * @param $intent
     */
    public function setIntent($intent ) {
        $this->intent = $intent;
    }


    /**
     * @return mixed
     */
    public function getPayerID(){
        return $this->payerID;
    }


    /**
     * @param $payerID
     */
    public function setPayerID($payerID ) {
        $this->payerID = $payerID;
    }

    /**
     * @return mixed
     */
    public function isUnauthorized()
    {
        return $this->unauthorized;
    }
}