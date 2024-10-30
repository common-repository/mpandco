<?php
namespace mPandco\API\Models;


/**
 * Class mpandcoItem
 * @package mPandco\API\Models
 */
class mpandcoItem  implements \JsonSerializable {

    /**
     * @var mixed
     */
    private $_name;

    /**
     * @var mixed
     */
    private $_quantity;

    /**
     * @var mixed
     */
    private $_sku;

    /**
     * @var mixed
     */
    private $_price;

    /**
     * @var mixed
     */
    private $_currency;

    /**
     * @return mixed
     */
    public function getName() {
		return $this->_name;
	}

    /**
     * @param $name
     */
    public function setName($name ) {
		$this->_name = $name;
	}

    /**
     * @return mixed
     */
    public function getQuantity() {
		return $this->_quantity;
	}


    /**
     * @param $quantity
     */
    public function setQuantity($quantity ) {
		$this->_quantity = $quantity;
	}

    /**
     * @return mixed
     */
    public function getSku(){
		return $this->_sku;
	}

    /**
     * @param $sku
     */
    public function setSku($sku ) {
		$this->_sku = $sku;
	}

    /**
     * @return mixed
     */
    public function getPrice() {
		return $this->_price;
	}

    /**
     * @param $price
     */
    public function setPrice($price ) {
		$this->_price = $price;
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
     * @return array|mixed
     */
    public function jsonSerialize() {
		return [
			'name' => $this->getName(),
			'quantity' => $this->getQuantity(),
			'sku' => $this->getSku(),
			'price' => $this->getPrice(),
			'currency' => $this->getCurrency()
		];
	}

}