<?php
namespace mPandco\API\Models;


/**
 * Class mpandcoRedirectUrls
 * @package mPandco\API\Models
 */
class mpandcoRedirectUrls implements \JsonSerializable {

    /**
     * @var
     */
    private $_returnUrl;
    /**
     * @var
     */
    private $_cancelUrl;

    /**
     * @return mixed
     */
    public function getReturnUrl() {
		return $this->_returnUrl;
	}

    /**
     * @param $returnUrl
     */
    public function setReturnUrl($returnUrl ) {
		$this->_returnUrl = $returnUrl;
	}

    /**
     * @return mixed
     */
    public function getCancelUrl(){
		return $this->_cancelUrl;
	}

    /**
     * @param $cancelUrl
     */
    public function setCancelUrl($cancelUrl ) {
		$this->_cancelUrl = $cancelUrl;
	}

    /**
     * @return array|mixed
     */
    public function jsonSerialize() {
		return [
			'returnUrl' => $this->getReturnUrl(),
			'cancelUrl' => $this->getCancelUrl()
		];
	}
}