<?php
use mPandco\API\Models\mpandcoPaymentIntent;
use mPandco\API\Models\mpandcoAmountDetails;
use mPandco\API\Models\mpandcoAmount;
use mPandco\API\Models\mpandcoRedirectUrls;
use mPandco\API\Models\mpandcoTransaction;
use mPandco\API\Models\mpandcoItem;


/**
 * Class mpandco_gateway_request
 */
class mpandco_gateway_request
{

    /**
     * @var mpandco_gateway
     */
    protected $gateway;

    /**
     * @var string
     */
    protected $intent;

    /**
     * @var mpandcoPaymentIntent
     */
    private $paymentIntent;

    /**
     * @var mpandcoRedirectUrls
     */
    private $redirectsUrls;

    /**
     * @var array
     */
    private $transactions;

    /**
     * @var mpandcoAmount
     */
    private $amount;

    /**
     * @var mpandcoAmountDetails
     */
    private $amountDetails;

    /**
     * @var array
     */
    private $items;

    /**
     * WC_Gateway_mPandco_Request constructor.
     * @param $gateway
     * @param string $intent
     */
    public function __construct($gateway, $intent = 'sale') {
        $this->gateway    = $gateway;
        $this->intent = $intent;
        $this->paymentIntent = new mpandcoPaymentIntent();
        $this->redirectsUrls = new mpandcoRedirectUrls();
        $this->transactions = array();
        $this->amount = new mpandcoAmount();
        $this->amountDetails = new mpandcoAmountDetails();
        $this->items = array();
    }

    /**
     * @param string $recipent
     * @return array|mixed
     */
    public function getArguments($recipent= '') {
        $this->paymentIntent->setIntent($this->intent);
        $this->paymentIntent->setRedirectUrls($this->redirectsUrls);
        $this->paymentIntent->setTransactions($this->transactions);
        $this->paymentIntent->setRecipient($recipent);
        return $this->paymentIntent->jsonSerialize();
    }

    /**
     * @param $returnurl
     * @param $cancelurl
     */
    public function setRedirects($returnurl, $cancelurl) {
        $this->redirectsUrls->setReturnUrl($returnurl);
        $this->redirectsUrls->setCancelUrl($cancelurl);
    }

    /**
     * @param WC_Order $order
     * @param $accountDestination
     * @param string $description
     */
    public function addTransaction(WC_Order $order, $accountDestination, $description = '') {
        $transaction = new mpandcoTransaction();
        $transaction->setDigitalAccountDestination($accountDestination);
        $transaction->setDescription($description);
        $transaction->setInvoiceNumber('order'.$order->get_order_number());
        $this->amount->setTotal($order->get_total());
        $this->amount->setCurrency('VES');
        $this->amountDetails->setShipping($order->get_shipping_total());
        $this->amountDetails->setTax($order->get_total_tax());
        $this->amountDetails->setSubTotal($order->get_total()-$order->get_total_tax()-$order->get_shipping_total());
        $this->amount->setDetails($this->amountDetails);
        $transaction->setAmount($this->amount);
        $this->orderItems($order);
        $transaction->setItems($this->items);
        array_push($this->transactions,$transaction);
    }

    /**
     * @param $order
     */
    protected function orderItems($order) {
        foreach ($order->get_items() as $i){
            $item = new mpandcoItem();
            $product =wc_get_product($i['product_id']);
            $item->setName($i->get_name());
            $item->setQuantity($i->get_quantity());
            $item->setSku($product->get_sku());
            $item->setPrice($product->get_price());
            $item->setCurrency('VES');
            array_push($this->items,$item);
        }
    }
}