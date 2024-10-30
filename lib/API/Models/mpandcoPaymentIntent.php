<?php

namespace mPandco\API\Models;


/**
 * Class mpandcoPaymentIntent
 * @package mPandco\API\Models
 */
class mpandcoPaymentIntent implements \JsonSerializable {

    /**
     * @var
     */
    private $_intent;
    /**
     * @var
     */
    private $_redirectUrls;
    /**
     * @var
     */
    private $_transactions;
    /**
     * @var
     */
    private $_recipient;

    /**
     * @return mixed
     */
    public function getIntent() {
		return $this->_intent;
	}

    /**
     * @param $intent
     */
    public function setIntent($intent ) {
		$this->_intent = $intent;
	}


    /**
     * @return mixed
     */
    public function getRedirectUrls() {
		return $this->_redirectUrls;
	}


    /**
     * @param $redirectUrls
     */
    public function setRedirectUrls($redirectUrls ) {
		$this->_redirectUrls = $redirectUrls;
	}

    /**
     * @return mixed
     */
    public function getTransactions() {
		return $this->_transactions;
	}

    /**
     * @param $transactions
     */
    public function setTransactions($transactions ) {
		$this->_transactions = $transactions;
	}

    /**
     * @return mixed
     */
    public function getRecipient() {
		return $this->_recipient;
	}

    /**
     * @param $recipient
     */
    public function setRecipient($recipient ) {
		$this->_recipient = $recipient;
	}

    /**
     * @return array|mixed
     */
    public function jsonSerialize() {
		return [
			'paymentintent' => array(
				'intent' => $this->getIntent(),
				'redirectUrls' => $this->getRedirectUrls(),
				'transactions' => $this->getTransactions(),
				'recipient' => $this->getRecipient()
			)
		];
	}

}