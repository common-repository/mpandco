<?php


namespace mPandco\API\Core;


/**
 * Class HttpConnection
 * @package mPandco\API\Core
 */
class HttpConnection
{
    /**
     * @var HttpConfig
     */
    private $httpConfig;

    /**
     * @var array
     */
    private static $retryCodes = array('408', '502', '503', '504',);

    /**
     * HttpConnection constructor.
     * @param HttpConfig $httpConfig
     */
    public function __construct(HttpConfig $httpConfig)
    {
        $this->httpConfig = $httpConfig;
    }


    /**
     * @return array
     */
    private function getHttpHeaders()
    {
        $ret = array();
        foreach ($this->httpConfig->getHeaders() as $k => $v) {
            $ret[] = "$k: $v";
        }
        return $ret;
    }

    /**
     * @param $data
     * @return array|\Exception
     */
    public function execute($data)
    {
        $ch = curl_init($this->httpConfig->getUrl());

        curl_setopt_array($ch, $this->httpConfig->getCurlOptions());
        $this->httpConfig->addHeader('Connection','close');
        curl_setopt($ch, CURLOPT_URL, $this->httpConfig->getUrl());
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHttpHeaders());
        switch ($this->httpConfig->getMethod()) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
        }

        if ($this->httpConfig->getMethod() != NULL) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->httpConfig->getMethod());
        }
        $result = curl_exec($ch);

        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch) == 60) {
            $result = curl_exec($ch);
        }
        $retries = 0;
        if (in_array($httpStatus, self::$retryCodes) && $this->httpConfig->getHttpRetryCount() != null) {
            do {
                $result = curl_exec($ch);
                $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            } while (in_array($httpStatus, self::$retryCodes) && (++$retries < $this->httpConfig->getHttpRetryCount()));
        }

        if (curl_errno($ch)) {
            $ex = new \Exception(
                $this->httpConfig->getUrl(),
                curl_error($ch),
                curl_errno($ch)
            );
            curl_close($ch);
            return $ex;
        }

        $responseHeaderSize = strlen($result) - curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
        $responseHeaders = substr($result, 0, $responseHeaderSize);
        $result = substr($result, $responseHeaderSize);

        curl_close($ch);
        $response = array(
            "data"      => json_decode($result,true),
            "header"    => self::get_headers_from_curl_response($responseHeaders),
            "status"    => $httpStatus
        );
        return $response;
    }


    /**
     * @param $headerContent
     * @return array
     */
    static function get_headers_from_curl_response($headerContent)
    {

        $headers = array();

        $arrRequests = explode("\r\n\r\n", $headerContent);
        for ($index = 0; $index < count($arrRequests) -1; $index++) {

            foreach (explode("\r\n", $arrRequests[$index]) as $i => $line)
            {
                if ($i === 0)
                    $headers[$index]['http_code'] = $line;
                else
                {
                    list ($key, $value) = explode(': ', $line);
                    $headers[$index][$key] = $value;
                }
            }
        }

        return $headers;
    }


}