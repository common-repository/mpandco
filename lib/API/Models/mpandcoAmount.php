<?php

namespace mPandco\API\Models;


/**
 * Class mpandcoAmount
 * @package mPandco\API\Models
 */
class mpandcoAmount implements \JsonSerializable {


    /**
     * @var mixed
     */
    private $_total;

    /**
     * @var mixed
     */
    private $_currency;

    /**
     * @var mixed
     */
    private $_details;


    /**
     * @return mixed
     */
    public function getTotal(){
		return $this->_total;
	}


    /**
     * @param $total
     */
    public function setTotal($total ) {
		$this->_total = $total;
	}


    /**
     * @return mixed
     */
    public function getCurrency(){
		return $this->_currency;
	}


    /**
     * @param $currency
     */
    public function setCurrency($currency ) {
		$this->_currency = $currency;
	}


    /**
     * @return mixed
     */
    public function getDetails() {
		return $this->_details;
	}


    /**
     * @param $details
     */
    public function setDetails($details ) {
		$this->_details = $details;
	}


    /**
     * @return array|mixed
     */
    public function jsonSerialize() {
		return [
			'total' => $this->getTotal(),
			'currency' => $this->getCurrency(),
			'details' => $this->getDetails()
		];
	}

}