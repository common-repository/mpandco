<?php

namespace mPandco\API\Models;


/**
 * Class mpandcoTransaction
 * @package mPandco\API\Models
 */
class mpandcoTransaction implements  \JsonSerializable {

    /**
     * @var
     */
    private $_digitalAccountDestination;

    /**
     * @var
     */
    private $_amount;

    /**
     * @var
     */
    private $_description;

    /**
     * @var
     */
    private $_invoiceNumber;

    /**
     * @var
     */
    private $_items;

    /**
     * @return mixed
     */
    public function getDigitalAccountDestination() {
		return $this->_digitalAccountDestination;
	}

    /**
     * @param $digitalAccountDestination
     */
    public function setDigitalAccountDestination($digitalAccountDestination ) {
		$this->_digitalAccountDestination = $digitalAccountDestination;
	}

    /**
     * @return mixed
     */
    public function getAmount() {
		return $this->_amount;
	}

    /**
     * @param $amount
     */
    public function setAmount($amount ) {
		$this->_amount = $amount;
	}

    /**
     * @return mixed
     */
    public function getDescription() {
		return $this->_description;
	}

    /**
     * @param $description
     */
    public function setDescription($description ) {
		$this->_description = $description;
	}

    /**
     * @return mixed
     */
    public function getInvoiceNumber() {
		return $this->_invoiceNumber;
	}

    /**
     * @param $invoiceNumber
     */
    public function setInvoiceNumber($invoiceNumber ) {
		$this->_invoiceNumber = $invoiceNumber;
	}

    /**
     * @return mixed
     */
    public function getItems() {
		return $this->_items;
	}

    /**
     * @param $items
     */
    public function setItems($items ) {
		$this->_items = $items;
	}


    /**
     * @return array|mixed
     */
    public function jsonSerialize() {
		return [
			'digitalAccountDestination' => $this->getDigitalAccountDestination(),
			'amount' => $this->getAmount(),
			'description' => $this->getDescription(),
			'invoiceNumber' => $this->getInvoiceNumber(),
			'items' => $this->getItems()
		];
	}

}