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

	/**
	 * Инициализация приватных свойств объекта.
	 * @return self.
	 */
	public function __construct() {
		$this->_serverMap	 = $_SERVER;
		$this->_requestMap	 = $_REQUEST;
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
	 * Возвращает URL-путь запроса.
	 * @return string|null 
	 */
	public function getUrlPath() {
		if (isset($this->_serverMap['REQUEST_URI'])) {
			$sUri	 = $this->_serverMap['REQUEST_URI'];
			$aUri	 = explode('?', $sUri);
			$mRes	 = $aUri[0];
		} else {
			$mRes = NULL;
		}
		return $mRes;
	}

	/**
	 * Метода возвращает значение параметра строки запроса по ключу либо всю 
	 * строку запроса как массив.
	 * @param string $sKey По умолчанию - пустая строка.
	 * @return array.
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
	 * Определяет является ли запрос ajax-запросом.
	 * @return boolean.
	 */
	public function isAjax() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
			&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) 
			== 'xmlhttprequest';
	}

}

