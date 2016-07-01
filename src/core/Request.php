<?php

namespace core;

/**
 * The class provides the centralized access to the parameters of input request.
 * @author coon
 */
class Request {

    /** @var array It contains the superglobal array $_SERVER */
    private $_serverMap = array();

    /** @var array It contains the superglobal array $_REQUEST. */
    private $_requestMap = array();

    /** @var array It contains the raw request body. */
    private $_rawBody = NULL;

    /**
     * The constructor inits the private properties of the object.
     * @return self.
     */
    public function __construct() {
        $this->_serverMap  = $_SERVER;
        $this->_requestMap = $_REQUEST;
        $this->_rawBody    = @file_get_contents('php://input');
    }

    /**
     * It returns the request HTTP-method.
     * @return string|null
     */
    public function getMethod() {
        return isset($this->_serverMap['REQUEST_METHOD'])
                ? $this->_serverMap['REQUEST_METHOD']
                : NULL;
    }

    /**
     * It returns the array of request headers.
     * @return string[]
     */
    public function getHeaders() {
        // for apache2 only method
        return getallheaders();
    }

    /** 
     * It returns the header by passed name. The search is case-insensitive.
     * @param string $sName
     * @return string|null
     */
    public function getHeaderByName($sName) {
        $mRes = NULL;
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
    public function getCookies() {
        $mRes       = NULL;
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
    public function getRawBody() {
        return $this->_rawBody;
    }

    /**
     * It returns trhe URL path of the request.
     * @return string|null 
     */
    public function getUrlPath() {
        if (isset($this->_serverMap['REQUEST_URI'])) {
            $sUri = $this->_serverMap['REQUEST_URI'];
            $aUri = explode('?', $sUri);
            $mRes = $aUri[0];
        } else {
            $mRes = NULL;
        }
        return $mRes;
    }

    /**
     * The method returns the string values of request by key or whole request 
     * string as array.
     * @param string $sKey It's empty string by default.
     * @return mixed.
     */
    public function getParam($sKey = '') {
        if (empty($sKey)) {
            return $this->_requestMap;
        } else {
            return isset($this->_requestMap[$sKey])
                    ? $this->_requestMap[$sKey]
                    : NULL;
        }
    }

    /**
     * The method returns the server parameter value by the key (from superglobal
     * array $_SERVER) or the full array.
     * @param string $sKey It's empty string by default.
     * @return mixed.
     */
    public function getServerParam($sKey = '') {
        if (empty($sKey)) {
            return $this->_serverMap;
        } else {
            return isset($this->_serverMap[$sKey])
                    ? $this->_serverMap[$sKey]
                    : NULL;
        }
    }

    /**
     * It checks if the request is an ajax request.
     * @return boolean.
     */
    public function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

}
