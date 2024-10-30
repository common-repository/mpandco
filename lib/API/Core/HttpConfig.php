<?php


namespace mPandco\API\Core;


/**
 * Class HttpConfig
 * @package mPandco\API\Core
 */
class HttpConfig
{
    /**
     * @var array
     */
    public static $defaultCurlOptions = array(
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HEADER => 1,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_USERAGENT => 'mPandco-WC-Wordpress',
        CURLOPT_HTTPHEADER => array(),
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_SSL_VERIFYPEER => 1
    );

    /**
     *
     */
    const HEADER_SEPARATOR = ";";

    /**
     *
     */
    const HTTP_GET = 'GET';

    /**
     *
     */
    const HTTP_POST = 'POST';

    /**
     *
     */
    const URL_DEFAULT_API = 'https://app.mpandco.com/';

    /**
     *
     */
    const URL_SANDBOX_API = 'https://test.mpandco.com/';

    /**
     * @var array
     */
    private $headers = array();

    /**
     * @var array
     */
    private $curlOptions;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $method;

    /**
     * @var int
     */
    private $retryCount;


    /**
     * HttpConfig constructor.
     * @param $url
     * @param bool $test
     * @param string $method
     * @param array $configs
     */
    public function __construct($url, $test = false, $method = self::HTTP_POST, $configs = array() ) {
        if ($test) $this->url = self::URL_SANDBOX_API.$url;
        else $this->url    = self::URL_DEFAULT_API.$url;
        $this->method = $method;
        $this->curlOptions = $this->getHttpConstantsFromConfigs('https.', $configs) + self::$defaultCurlOptions;
        $curl = curl_version();
        $sslVersion = isset($curl['ssl_version']) ? $curl['ssl_version'] : '';
        if (substr_compare($sslVersion, "NSS/", 0, strlen("NSS/")) === 0) {
            $this->removeCurlOption(CURLOPT_SSL_CIPHER_LIST);
        }
        $this->retryCount = 2;
    }

    /**
     * @param $prefix
     * @param array $configs
     * @return array
     */
    public function getHttpConstantsFromConfigs($prefix, $configs = array())
    {
        $arr = array();
        if ($prefix != null && is_array($configs)) {
            foreach ($configs as $k => $v) {
                // Check if it startsWith
                if (substr($k, 0, strlen($prefix)) === $prefix) {
                    $newKey = ltrim($k, $prefix);
                    if (defined($newKey)) {
                        $arr[constant($newKey)] = $v;
                    }
                }
            }
        }
        return $arr;
    }

    /**
     * @param $name
     */
    public function removeCurlOption($name)
    {
        unset($this->curlOptions[$name]);
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return array
     */
    public function getCurlOptions()
    {
        return $this->curlOptions;
    }

    /**
     * @param $name
     * @param $value
     * @param bool $overWrite
     */
    public function addHeader($name, $value, $overWrite = true)
    {
        if (!array_key_exists($name, $this->headers) || $overWrite) {
            $this->headers[$name] = $value;
        } else {
            $this->headers[$name] = $this->headers[$name] . self::HEADER_SEPARATOR . $value;
        }
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return int
     */
    public function getHttpRetryCount()
    {
        return $this->retryCount;
    }
}