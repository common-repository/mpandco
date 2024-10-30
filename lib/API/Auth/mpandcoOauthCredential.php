<?php


namespace mPandco\API\Auth;


/**
 * Class mpandcoOauthCredential
 * @package mPandco\API\Auth
 */
class mpandcoOauthCredential {
    /**
     *
     */
    const URL_DEFAULT_API = 'https://app.mpandco.com/';

    /**
     *
     */
    const URL_SANDBOX_API = 'https://test.mpandco.com/';
    /**
     *
     */
    const URL_AUTH = 'oauth/v2/token';


    /**
     * @var int
     */
    private static $expiryBufferTime = 60;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var string
     */
    private $tokenExpiresIn;

    /**
     * @var string
     */
    private $tokenCreatetime;

    /**
     * @var string
     */
    private $tokenType;


    /**
     * @var bool
     */
    private  $test;

    /**
     * @var mixed
     */
    private $response;


    /**
     * OAuthTokenCredential constructor.
     * @param $clientId
     * @param $clientSecret
     * @param bool $test
     */
    public function __construct($clientId, $clientSecret, $test = false)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->test = $test;
        if ($this->test){
            $this->accessToken = get_option('Payment_mPandco_accessToken_sandbox');
        }else{
            $this->accessToken = get_option('Payment_mPandco_accessToken');
        }
    }

    /**
     * @return bool
     */
    public function existAccessToken(){
        return $this->accessToken!=false;
    }


    /**
     * @return mixed|string
     */
    public function getAccessToken(){
        $this->tokenCreatetime = $this->test? get_option('Payment_mPandco_tokenCreateTime_sandbox'): get_option('Payment_mPandco_tokenCreateTime');
        $this->tokenExpiresIn = $this->test? get_option('Payment_mPandco_tokenExpiresIn_sandbox'): get_option('Payment_mPandco_tokenExpiresIn');
        $this->tokenType = get_option('Payment_mPandco_tokenType');
        if ($this->accessToken){
            if ((time()-$this->tokenCreatetime) < ($this->tokenExpiresIn - self::$expiryBufferTime)){
                return $this->accessToken;
            }else{
                return $this->refreshAccessToken();
            }
        }else{
            return '';
        }
    }


    /**
     * @return mixed|string
     */
    public function refreshAccessToken()
    {
        $params = array();
        $this->refreshToken = $this->test? get_option('Payment_mPandco_refreshToken_sandbox'): get_option('Payment_mPandco_refreshToken');
        if ($this->refreshToken){
            $basepath = $this->test? self::URL_SANDBOX_API: self::URL_DEFAULT_API;
            $params['grant_type'] = 'refresh_token';
            $params['refresh_token'] = $this->refreshToken;
            $params['client_id'] = $this->clientId;
            $params['client_secret'] = $this->clientSecret;
            $payload = http_build_query($params);
            $request = wp_remote_post($basepath.self::URL_AUTH,array('body'=> $payload));
            $this->response = json_decode(wp_remote_retrieve_body($request),true);
            if (is_wp_error( $this->response ) || wp_remote_retrieve_response_code($this->response)!= 200){
                $this->accessToken = null;
                $this->tokenExpiresIn = null;
                return '';
            }else if (!isset($this->response['access_token']) || !isset($this->response['expires_in'])){
                $this->accessToken = null;
                $this->tokenExpiresIn = null;
                return '';

            }else{
                $this->accessToken = $this->response['access_token'];
                $this->tokenExpiresIn = $this->response['expires_in'];
                $this->refreshToken = $this->response['refresh_token'];
                $this->tokenType = $this->response['token_type'];
                $this->tokenCreatetime = time();
                if ($this->test){
                    update_option('Payment_mPandco_accessToken_sandbox',$this->accessToken);
                    update_option('Payment_mPandco_refreshToken_sandbox',$this->refreshToken);
                    update_option('Payment_mPandco_tokenType',$this->tokenType);
                    update_option('Payment_mPandco_tokenExpiresIn_sandbox',$this->tokenExpiresIn);
                    update_option('Payment_mPandco_tokenCreateTime_sandbox',$this->tokenCreatetime);
                }else{
                    update_option('Payment_mPandco_accessToken',$this->accessToken);
                    update_option('Payment_mPandco_refreshToken',$this->refreshToken);
                    update_option('Payment_mPandco_tokenType',$this->tokenType);
                    update_option('Payment_mPandco_tokenExpiresIn',$this->tokenExpiresIn);
                    update_option('Payment_mPandco_tokenCreateTime',$this->tokenCreatetime);
                }
                return $this->accessToken;
            }
        }else{
            return '';
        }
    }


    /**
     * @return bool
     */
    public function AuthUser() {
        $params = array();
        $response = null;
        if ($this->clientId && $this->clientSecret){
            $basepath = $this->test? self::URL_SANDBOX_API: self::URL_DEFAULT_API;
            $params['grant_type'] = 'urn:client_apps';
            $params['client_id'] = $this->clientId;
            $params['client_secret'] = $this->clientSecret;
            $payload = http_build_query($params);
            $request = wp_remote_post($basepath.self::URL_AUTH,array('body'=> $payload));
            $this->response = json_decode(wp_remote_retrieve_body($request),true);
            if (is_wp_error( $this->response ) || wp_remote_retrieve_response_code($request)!=200) return false;
            $this->accessToken = $this->response['access_token'];
            $this->tokenExpiresIn = $this->response['expires_in'];
            $this->refreshToken = $this->response['refresh_token'];
            $this->tokenType = $this->response['token_type'];
            $this->tokenCreatetime = time();
            if ($this->test){
                update_option('Payment_mPandco_accessToken_sandbox',$this->accessToken);
                update_option('Payment_mPandco_refreshToken_sandbox',$this->refreshToken);
                update_option('Payment_mPandco_tokenType',$this->tokenType);
                update_option('Payment_mPandco_tokenExpiresIn_sandbox',$this->tokenExpiresIn);
                update_option('Payment_mPandco_tokenCreateTime_sandbox',$this->tokenCreatetime);
            }else{
                update_option('Payment_mPandco_accessToken',$this->accessToken);
                update_option('Payment_mPandco_refreshToken',$this->refreshToken);
                update_option('Payment_mPandco_tokenType',$this->tokenType);
                update_option('Payment_mPandco_tokenExpiresIn',$this->tokenExpiresIn);
                update_option('Payment_mPandco_tokenCreateTime',$this->tokenCreatetime);
            }
            return true;
        }
        return false;
    }


    /**
     * @return mixed
     */
    public function getTokenType() {
        $this->tokenType = get_option('Payment_mPandco_tokenType');
        return $this->tokenType;
    }

    /**
     *
     */
    public function reset()
    {
        delete_option('Payment_mPandco_accessToken_sandbox');
        delete_option('Payment_mPandco_refreshToken_sandbox');
        delete_option('Payment_mPandco_tokenType');
        delete_option('Payment_mPandco_tokenExpiresIn_sandbox');
        delete_option('Payment_mPandco_tokenCreateTime_sandbox');
        delete_option('Payment_mPandco_accessToken');
        delete_option('Payment_mPandco_refreshToken');
        delete_option('Payment_mPandco_tokenType');
        delete_option('Payment_mPandco_tokenExpiresIn');
        delete_option('Payment_mPandco_tokenCreateTime');
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

}