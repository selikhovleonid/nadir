<?php

namespace core;

/**
 * The class provides the processing of page headers.
 * @author coon
 */
class Headers {

    /** @var self The singleton object of current class. */
    private static $_instance = NULL;

    /** @var string[] The headers of the page stack. */
    protected $headerList = array();

    /** @var boolean The flag is equal TRUE when the page headers are set. */
    protected $isRan = FALSE;

    /**
     * @ignore.
     */
    private function __construct() {
        // Nothing here
    }

    /**
     * It returns the singleton instance of current class.
     * @return self.
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * It returns the human readable explanation of HTTP status code.
     * @param integer $nCode The status code.
     * @return string The description.
     * @throws \core\Exception It throws if unknown HTTP code was passed.
     */
    public static function getHTTPExplanationByCode($nCode) {
        switch ((int) $nCode) {
            case 100: return 'Continue';
            case 101: return 'Switching Protocols';
            case 200: return 'OK';
            case 201: return 'Created';
            case 202: return 'Accepted';
            case 203: return 'Non-Authoritative Information';
            case 204: return 'No Content';
            case 205: return 'Reset Content';
            case 206: return 'Partial Content';
            case 300: return 'Multiple Choices';
            case 301: return 'Moved Permanently';
            case 302: return 'Moved Temporarily';
            case 303: return 'See Other';
            case 304: return 'Not Modified';
            case 305: return 'Use Proxy';
            case 400: return 'Bad Request';
            case 401: return 'Unauthorized';
            case 402: return 'Payment Required';
            case 403: return 'Forbidden';
            case 404: return 'Not Found';
            case 405: return 'Method Not Allowed';
            case 406: return 'Not Acceptable';
            case 407: return 'Proxy Authentication Required';
            case 408: return 'Request Time-out';
            case 409: return 'Conflict';
            case 410: return 'Gone';
            case 411: return 'Length Required';
            case 412: return 'Precondition Failed';
            case 413: return 'Request Entity Too Large';
            case 414: return 'Request-URI Too Large';
            case 415: return 'Unsupported Media Type';
            case 429: return 'Stop spam';
            case 500: return 'Internal Server Error';
            case 501: return 'Not Implemented';
            case 502: return 'Bad Gateway';
            case 503: return 'Service Unavailable';
            case 504: return 'Gateway Time-out';
            case 505: return 'HTTP Version not supported';
            default:
                throw new Exception('Unknown HTTP code');
        }
    }

    /**
     * It adds the header to the stack.
     * @param string $sHeader The page header.
     * @return self.
     * @throws \core\Exception It throws if passed header was already added earlier.
     */
    public function add($sHeader) {
        foreach ($this->headerList as $sTmp) {
            if ($sTmp == $sHeader) {
                throw new Exception("'{$sHeader}' header already added.");
            }
        }
        $this->headerList[] = $sHeader;
        return self::$_instance;
    }

    /**
     * It adds the header to the stack by HTTP code.
     * @param integer $nCode The code.
     * @return self.
     */
    public function addByHttpCode($nCode) {
        $sProtocol = isset($_SERVER['SERVER_PROTOCOL'])
                ? $_SERVER['SERVER_PROTOCOL']
                : 'HTTP/1.1';
        $sHeader   = "{$sProtocol} {$nCode} "
                . self::getHTTPExplanationByCode($nCode);
        return $this->add($sHeader);
    }

    /**
     * The method returns the stack of page headers.
     * @return string[].
     */
    public function getAll() {
        return $this->headerList;
    }

    /**
     * The method returns TRUE if the the page headers were set.
     * @return boolean.
     */
    public function isRan() {
        return $this->isRan;
    }

    /**
     * The main execution method. It sets all added headers into the page.
     * @return void.
     */
    public function run() {
        $this->isRan = TRUE;
        foreach ($this->headerList as $sHeader) {
            header($sHeader);
        }
    }

}
