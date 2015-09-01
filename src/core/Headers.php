<?php

/**
 * Класс отвечает за генерацию заголовков страницы.
 * @author coon
 */

namespace core;

class Headers {

    /** @var self Объект-singleton текущего класса. */
    private static $_instance = NULL;

    /** @var string[] Стек заголовков страницы. */
    private $_headerList = array();

    /**
     * @ignore.
     */
    private function __construct() {
        // nothing here
    }

    /**
     * Возвращает singleton-экземпляр текущего класса.
     * @return self.
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Возвращает человеко-читаемое описание HTTP-кода состояния.
     * @param integer $nCode Код состояния.
     * @return string Описание.
     * @throws Exception
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
     * Добавляет заголовок в стек.
     * @param string $sHeader Заголовок страницы.
     * @return self.
     */
    public function add($sHeader) {
        $this->_headerList[] = $sHeader;
        return self::$_instance;
    }

    /**
     * Добавить заголовок страницы в стек по коду состояния.
     * @param integer $nCode Код.
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
     * Устанавливает все сохраненные в стеке заголовки в страницу.
     * @return void.
     */
    public function run() {
        foreach ($this->_headerList as $sHeader) {
            header($sHeader);
        }
    }

}
