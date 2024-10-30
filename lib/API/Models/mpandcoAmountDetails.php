<?php
namespace mPandco\API\Models;


/**
 * Class mpandcoAmountDetails
 * @package mPandco\API\Models
 */
class mpandcoAmountDetails implements  \JsonSerializable {


    /**
     * @var mixed
     */
    private $_shipping;

    /**
     * @var mixed
     */
    private $_tax;

    /**
     * @var mixed
     */
    private $_subTotal;

    /**
     * @return mixed
     */
    public function getShipping() {
		return $this->_shipping;
	}

    /**
     * @param $shipping
     */
    public function setShipping($shipping ) {
		$this->_shipping = $shipping;
	}

    /**
     * @return mixed
     */
    public function getTax() {
		return $this->_tax;
	}

    /**
     * @param $tax
     */
    public function setTax($tax ){
		$this->_tax = $tax;
	}

    /**
     * @return mixed
     */
    public function getSubTotal() {
		return $this->_subTotal;
	}


    /**
     * @param $subTotal
     */
    public function setSubTotal($subTotal ) {
		$this->_subTotal = $subTotal;
	}


    /**
     * @return array|mixed
     */
    public function jsonSerialize() {
		return [
			'shipping' => $this->getShipping(),
			'tax' => $this->getTax(),
			'subTotal' => $this->getSubTotal()
		];
	}

}