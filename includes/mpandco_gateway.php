<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use mPandco\API\Auth\mpandcoOauthCredential;
use mPandco\API\Payment\mpandcoPaymentSale;
use mPandco\API\Models\mpancoPaymentExecuteResponse;

/**
 * Class mpandco_gateway
 */
class mpandco_gateway extends WC_Payment_Gateway
{

    /**
     * @var bool
     */
    public static $log_enabled = false;

    /**
     * @var bool
     */
    public static $log = false;

    /**
     * @var bool
     */
    private static $sandbox = false;

    /**
     * @var bool
     */
    private static $credentials = false;

    /**
     * @var null
     */
    private  static $Auth = null;

    /**
     * WC_Gateway_mPandco constructor.
     */
    public function __construct()
    {
        $this->id = 'mpandco_gateway';
        $this->icon               = apply_filters('woocommerce_offline_icon', '');
        $this->has_fields = false;
        $this->supports     = array(
            'products'
        );
        $this->order_button_text    = __( 'Proceder con mPandco', 'mpandco' );
        $this->method_title         = __( 'mPandco Payment', 'mpandco' );
        $this->method_description   = __( 'Metodo de pago electonico mPandco', 'mpandco' );
        $this->init_form_fields();
        $this->init_settings();

        self::$log_enabled = 'yes' === $this->get_option('debug','no');
        self::$sandbox = 'yes' === $this->get_option( 'testmode', 'no' );
        $this->title            = $this->get_option( 'title',__('mpandco'));
        $this->description      = $this->get_option( 'description',__('Pagar via mPandco','mpandco'));
        if (self::isSandbox()){
            $this->description .= sprintf(__('<br>Realiza tu pago en tiempo real con mPandco, sino tienes una cuenta puedes registrarte gratis en <a href="https://test.mpandco.com">Sandbox mPandco</a> <b> (Modo de pruebas activo, no se usara dinero real) </b>'));
        }else{
            $this->description .= sprintf(__('<br>Realiza tu pago en tiempo real con mPandco, sino tienes una cuenta puedes registrarte gratis en <a href="https://app.mpandco.com"> mPandco</a>'));
        }

        if (self::isSandbox()){
            $this->get_credentials(
                $this->get_option('sandbox_api_id'),
                $this->get_option('sandbox_api_secret')
            );
        }else {
            $this->get_credentials(
                $this->get_option('api_id'),
                $this->get_option('api_secret')
            );
        }


        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action('woocommerce_thankyou_mpandco_gateway', array($this,'payment_sale'), 10, 1);
        add_action('wp_enqueue_scripts',array($this,'payment_scripts'));
    }

    /**
     * @return string|void
     */
    public function init_form_fields()
    {
        $this->form_fields = include 'settings_fields.php';
    }


    /**
     * @return mixed|string
     */
    public function get_icon()
    {
        $base_country = WC()->countries->get_base_country();
        if ( empty( $base_country ) ) {
            return '';
        }
        $icon_html = '<img src="'.plugins_url('/assets/img/mpandco_PM_payment_gateway_small.png',__DIR__).'" alt="' . __( 'mPandco acceptance mark', 'mpandco' ) . '" />';
        return apply_filters( 'woocommerce_gateway_icon', $icon_html, $this->id );
    }

    /**
     * @param $message
     * @param string $level
     */
    public function log($message, $level = 'info' ) {
        if ( self::$log_enabled ) {
            if ( empty( self::$log ) ) {
                self::$log = wc_get_logger();
            }
            self::$log->log( $level, $message, array( 'source' => 'mpandco' ) );
        }
    }

    /**
     * @return bool
     */
    public function process_admin_options() {
        $saved = parent::process_admin_options();
        if ( 'yes' !== $this->get_option( 'debug', 'no' ) ) {
            if ( empty( self::$log ) ) {
                self::$log = wc_get_logger();
            }
            self::$log->clear( 'mpandco' );
        }
        if(self::$Auth) self::$Auth->reset();
        return $saved;
    }

    /**
     * @param $id
     * @param $secret
     */
    public function get_credentials($id, $secret) {
        if ($id && $secret){
            self::$credentials = true;
            self::$Auth = new mpandcoOauthCredential(
                $id,
                $secret,
                self::$sandbox
            );
        }
    }

    /**
     * @return bool
     */
    public function mpandco_check_token() {
            if (!self::$Auth->existAccessToken()){
                if (!self::$Auth->AuthUser()){
                    $message= __('Falla en Auth, comuniquese con el administrador del sitio','mpandco');
                    wc_add_notice($message,'error');
                    $this->log(json_encode(self::$Auth->getResponse()));
                    return false;
                }else{
                    return true;
                }
            }else{
                $accessToken= self::$Auth->getAccessToken();
                if (!$accessToken){
                    $this->log(json_encode(self::$Auth->getResponse()));
                    if (!self::$Auth->AuthUser()){
                        $message= __('Falla en Auth, comuniquese con el administrador del sitio','mpandco');
                        wc_add_notice($message,'error');
                        $this->log(json_encode(self::$Auth->getResponse()));
                        return false;
                    }
                }
                return true;
            }
    }


    /**
     * @param int $order_id
     * @return array
     */
    public function process_payment($order_id ) {
        $accessToken= null;
        if (!self::$credentials){
            $message = __('Metodo de pago mPandco no ha sido configurado, comuniquese con el administrador del sitio','mpandco');
            wc_add_notice($message,'error');
            return array(
                'result' => 'failed',
                'redirect' => ''
            );
        }
        if ($this->mpandco_check_token()){
            $accessToken = self::$Auth->getAccessToken();
        }
        else
        {
            return array(
                'result' => 'failed',
                'redirect' => ''
            );
        }

        $order = new WC_Order($order_id);
        $request = new mpandco_gateway_request($this);
        $request->setRedirects(
            esc_url_raw( add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) ),
            esc_url_raw( $order->get_cancel_order_url_raw() ));
        $desc = __('Compra en ','mpandco').get_bloginfo( 'url' ).__('- orden #','mpandco').$order->get_order_number();
        $desc = substr($desc,0,70);
        $request->addTransaction(
            $order,
            $this->get_option('api_username'),
            $desc);
        $datarequest = $request->getArguments();
        $Payment = new mpandcoPaymentSale($datarequest,$accessToken,self::isSandbox());
        if ($accessToken){
            $request= $Payment->execute();
            if (!$request){
                $this->log(json_encode($Payment->getResponse()));
                if ($Payment->isUnauthorized()){
                    $message = sprintf(__('Petición denegada por mPandco, vuelva a intentar <br> si el problema persiste comuniquese con el administrador del sitio es posible que el acceso a mPandco sea incorrecto','mpandco'));
                    self::$Auth->reset();
                    wc_add_notice($message,'error');
                }else {
                    $message = __('Datos enviados a mPandco son incorrectos','mpandco');
                    wc_add_notice($message,'error');
                }
                return array(
                    'result' => 'failed',
                    'redirect' => ''
                );
            }

            return array(
                'result'   => 'success',
                'redirect' => $request['_links']['approval_url']['href'],
            );

        }
        return array(
            'result' => 'failed',
            'redirect' => ''
        );
    }

    /**
     * @param $order_id
     * @return int
     */
    public function payment_sale($order_id) {

        $order = new WC_Order( $order_id );
        if ($order->get_status()=='completed'){
            include_once dirname(__DIR__) . '/templates/mpandcoOrder_completed.php';
            $templateComplete = new  mpandcoOrder_completed($order);
            $templateComplete->render();
            return 1;
        }

        $accessToken = false;
        if (!self::$credentials){
            $message = __('Metodo de pago mPandco no ha sido configurado, comuniquese con el administrador del sitio','mpandco');
            wc_add_notice($message,'error');
            return 0;
        }
        if ($this->mpandco_check_token())
            $accessToken = self::$Auth->getAccessToken();
        else{
            $message = __('No se ha podido completar el pago','mpandco');
            wc_add_notice($message);
            return 0;
        };
        $paymentIntent = sanitize_text_field($_GET['paymentIntent']);
        $payerId = sanitize_text_field($_GET['payerId']);
        if (!isset($paymentIntent) && !isset($payerId)) return 0;

        $PaymentExecution = new mpandco_gateway_execute($this,$accessToken,self::isSandbox());
        $PaymentExecution->setIntent($paymentIntent);
        $PaymentExecution->setPayerID($payerId);

        $request = $PaymentExecution->mpandco_execute();
        if (!$request){
            $this->log(json_encode($request));
            if ($PaymentExecution->isUnauthorized()){
                $message = sprintf(__('Petición denegada por mPandco, vuelva a intentar <br> si el problema persiste comuniquese con el administrador del sitio es posible que el acceso a mPandco sea incorrecto','mpandco'));
                self::$Auth->reset();
                wc_add_notice($message,'error');
            }else {
                $message = __('Error al completar proceso de pago, pago cancelado','mpandco');
                wc_add_notice($message,'error');
            }
        }else{
            $responseModel = New mpancoPaymentExecuteResponse();
            $responseModel->setId($request['id']);
            $responseModel->setState($request['state']);
            $responseModel->setHistoryResponse($request['redirect_urls']['history_responses']);
            $responseModel->setTotal($request['total']);
            $responseModel->setTransactions($request['transactions']);
            $order->add_meta_data('PaymentSaleResponse_mPandco',serialize($responseModel->jsonSerialize()));
            $order->update_status('completed');
            include_once dirname(__DIR__) . '/templates/mpandcoOrder_Payment_Success.php';
            $templateSuccess = new  mpandcoOrder_Payment_Success($order);
            $templateSuccess->render();
        }
        return 1;
    }


    /**
     *
     */
    public function payment_fields() {
        if ($description = $this->get_description()) {
            echo wpautop(wptexturize($description));
        }
    }

    /**
     *
     */
    public function payment_scripts() {
        wp_register_style( 'mpandco_payment_style', plugins_url( '/assets/css/mpandco_payment_style.css',__DIR__),null,1);
        wp_enqueue_style('mpandco_payment_style');
    }

    /**
     * @return bool
     */
    public static function isSandbox()
    {
        return self::$sandbox;
    }

    /**
     * @return bool
     */
    public static function isCredentials()
    {
        return self::$credentials;
    }

    /**
     * @return null
     */
    public static function getAuth()
    {
        return self::$Auth;
    }
}