<?php

/**
 * Класс отвечает за централизованный доступ к параметрам входящего запроса.
 * @author coon
 */

namespace core;

class Request {

    /** @var array Содержит суперглобальный массив $_SERVER. */
    private $_serverMap = array();

    /** @var array Содержит суперглобальный массив $_REQUEST. */
    private $_requestMap = array();
    
    /** @var array Содержит необработанное тело запроса. */
    private $_rawBody    = NULL;

    /**
     * Инициализация приватных свойств объекта.
     * @return self.
     */
    public function __construct() {
        $this->_serverMap  = $_SERVER;
        $this->_requestMap = $_REQUEST;
        $this->_rawBody    = @file_get_contents('php://input');
    }

    /**
     * Возвращает HTTP-метод запроса.
     * @return string|null
     */
    public function getMethod() {
        return isset($this->_serverMap['REQUEST_METHOD']) 
            ? $this->_serverMap['REQUEST_METHOD'] 
            : NULL;
    }

    /**
     * Возвращает массив заголовков запроса.
     * @return string[]
     */
    public function getHeaders() {
        // for apache2 only method
        return getallheaders();
    }

    /**
     * Метод возвращает ассоциативный массив куки (ключи - имена куки).
     * @return array.
     */
    public function getCookies() {
        $mRes     = NULL;
        $aHeaders = $this->getHeaders();
        if (isset($aHeaders['Cookie'])) {
            $aCookies = explode(';', $aHeaders['Cookie']);
            foreach ($aCookies as $sCookie) {
                $aParts                 = explode('=', $sCookie);
                $mRes[trim($aParts[0])] = trim($aParts[1]);
            }
        }
        return $mRes;
    }

    /**
     * Метод возвращает необработанное тело запроса, полученное с потока ввода,
     * как строку.
     * @return string.
     */
    public function getRawBody() {
        return $this->_rawBody;
    }

    /**
     * Возвращает URL-путь запроса.
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
     * Метода возвращает значение параметра строки запроса по ключу либо всю 
     * строку запроса как массив.
     * @param string $sKey По умолчанию - пустая строка.
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
     * Метод возвращает значение серверного параметра по ключу (суперглобальный
     * массив $_SERVER), либо весь массив.
     * @param string $sKey По умолчанию - пустая строка.
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
     * Определяет является ли запрос ajax-запросом.
     * @return boolean.
     */
    public function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

}
