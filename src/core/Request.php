<?php

namespace core;

/**
 * The class provides the centralized access to the parameters of input request.
 * @author coon
 */
class Request
{
    /** @var array It contains the superglobal array $_SERVER */
    private $serverMap = array();

    /** @var array It contains the superglobal array $_REQUEST. */
    private $requestMap = array();

    /** @var array It contains the raw request body. */
    private $rawBody = null;

    /**
     * The constructor inits the private properties of the object.
     * @return self.
     */
    public function __construct()
    {
        $this->serverMap  = $_SERVER;
        $this->requestMap = $_REQUEST;
        $this->rawBody    = @file_get_contents('php://input');
    }

    /**
     * It returns the request HTTP-method.
     * @return string|null
     */
    public function getMethod()
    {
        return isset($this->serverMap['REQUEST_METHOD']) ? $this->serverMap['REQUEST_METHOD']
                : null;
    }

    /**
     * It returns the array of request headers.
     * @return string[]
     */
    public function getHeaders()
    {
        // for apache2 only method
        return getallheaders();
    }

    /**
     * It returns the header by passed name. The search is case-insensitive.
     * @param string $sName
     * @return string|null
     */
    public function getHeaderByName($sName)
    {
        $mRes = null;
        foreach ($this->getHeaders() as $sKey => $sValue) {
            if (strtolower($sKey) === strtolower($sName)) {
                $mRes = $sValue;
                break;
            }
        }
        return $mRes;
    }

    /**
     * The method returns the associated array of cookies.
     * @return array.
     */
    public function getCookies()
    {
        $mRes       = null;
        $mRawCookie = $this->getHeaderByName('Cookie');
        if (!is_null($mRawCookie)) {
            $aCookies = explode(';', $mRawCookie);
            foreach ($aCookies as $sCookie) {
                $aParts = explode('=', $sCookie);
                if (count($aParts) > 1) {
                    $mRes[trim($aParts[0])] = trim($aParts[1]);
                }
            }
        }
        return $mRes;
    }

    /**
     * The method returns the raw body of the request as string, which was gotten 
     * from the input stream.
     * @return string.
     */
    public function getRawBody()
    {
        return $this->rawBody;
    }

    /**
     * It returns trhe URL path of the request.
     * @return string|null 
     */
    public function getUrlPath()
    {
        if (isset($this->serverMap['REQUEST_URI'])) {
            $sUri = $this->serverMap['REQUEST_URI'];
            $aUri = explode('?', $sUri);
            $mRes = $aUri[0];
        } else {
            $mRes = null;
        }
        return $mRes;
    }

    /**
     * The method returns the string values of request by key or whole request 
     * string as array.
     * @param string $sKey It's empty string by default.
     * @return mixed.
     */
    public function getParam($sKey = '')
    {
        if (empty($sKey)) {
            return $this->requestMap;
        } else {
            return isset($this->requestMap[$sKey]) ? $this->requestMap[$sKey] : null;
        }
    }

    /**
     * The method returns the server parameter value by the key (from superglobal
     * array $_SERVER) or the full array.
     * @param string $sKey It's empty string by default.
     * @return mixed.
     */
    public function getServerParam($sKey = '')
    {
        if (empty($sKey)) {
            return $this->serverMap;
        } else {
            return isset($this->serverMap[$sKey]) ? $this->serverMap[$sKey] : null;
        }
    }

    /**
     * It checks if the request is an ajax request.
     * @return boolean.
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])
            == 'xmlhttprequest';
    }
}