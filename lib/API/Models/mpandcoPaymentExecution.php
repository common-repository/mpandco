<?php
namespace mPandco\API\Models;


/**
 * Class mpandcoPaymentExecution
 * @package mPandco\API\Models
 */
class mpandcoPaymentExecution implements \JsonSerializable {

    /**
     * @var mixed
     */
    private $_paymentIntent;

    /**
     * @var mixed
     */
    private $payer;

    /**
     * @return mixed
     */
    public function getPaymentIntent() {
		return $this->_paymentIntent;
	}

    /**
     * @param $paymentIntent
     */
    public function setPaymentIntent($paymentIntent ) {
		$this->_paymentIntent = $paymentIntent;
	}

    /**
     * @return mixed
     */
    public function getPayer() {
		return $this->payer;
	}


    /**
     * @param $payer
     */
    public function setPayer($payer ) {
		$this->payer = $payer;
	}


    /**
     * @return array|mixed
     */
    public function jsonSerialize() {
		return [
			'payment_execution' => array(
				'paymentIntent' => $this->getPaymentIntent(),
				'payer' => $this->getPayer()
			)
		];
	}

}