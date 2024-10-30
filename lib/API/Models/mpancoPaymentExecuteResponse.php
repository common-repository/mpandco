<?php

namespace mPandco\API\Models;


/**
 * Class mpandcoPaymentExecuteResponse
 * @package mPandco\API\Models
 */
class mpancoPaymentExecuteResponse implements \JsonSerializable {

    /**
     * @var mixed
     */
    private $id;

    /**
     * @var mixed
     */
    private $state;

    /**
     * @var mixed
     */
    private $historyResponse;

    /**
     * @var mixed
     */
    private $total;

    /**
     * @var mixed
     */
    private $transactions;

    /**
     * @return mixed
     */
    public function getId(){
		return $this->id;
	}

    /**
     * @param $id
     */
    public function setId($id ) {
		$this->id = $id;
	}

    /**
     * @return mixed
     */
    public function getState() {
		return $this->state;
	}

    /**
     * @param $state
     */
    public function setState($state ) {
		$this->state = $state;
	}

    /**
     * @return mixed
     */
    public function getHistoryResponse(){
		return $this->historyResponse;
	}

    /**
     * @param array $historyResponse
     */
    public function setHistoryResponse(array $historyResponse ) {
		$this->historyResponse = $historyResponse;
	}

    /**
     * @return mixed
     */
    public function getTotal(){
		return $this->total;
	}

    /**
     * @param $total
     */
    public function setTotal($total ) {
		$this->total = $total;
	}

    /**
     * @return mixed
     */
    public function getTransactions() {
		return $this->transactions;
	}

    /**
     * @param array $transactions
     */
    public function setTransactions(array $transactions ) {
		$this->transactions = $transactions;
	}


    /**
     * @return array|mixed
     */
    public function jsonSerialize() {
		return [
			'id' => $this->getId(),
			'state' => $this->getState(),
			'history' => $this->getHistoryResponse(),
			'total' =>json_decode(json_encode( $this->getTotal()), true),
			'transaction' => $this->getTransactions()
		];
	}
}