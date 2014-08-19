<?php

/**
 * Description of Request
 *
 * @author coon
 */

namespace core;

class Request {

	private $_serverMap	 = array();
	private $_requestMap = array();

	public function __construct() {
		$this->_serverMap = $_SERVER;
		$this->_requestMap = $_REQUEST;
	}

	public function getMethod() {
		return isset($this->_serverMap['REQUEST_METHOD']) ? $this->_serverMap['REQUEST_METHOD'] : NULL;
	}

	public function getUri() {
		if (isset($this->_serverMap['REQUEST_URI'])) {
			$sUri = $this->_serverMap['REQUEST_URI'];
			$aUri = explode('?', $sUri);
			$mRes = $aUri[0];
		} else {
			$mRes = NULL;
		}
		return $mRes;
	}
	
	public function getParam($sKey = '') {
		if (empty($sKey)) {
			return $this->_requestMap;
		} else {
			return isset($this->_requestMap[$sKey]) ? $this->_requestMap[$sKey] : NULL;
		}
		
	}
	
	public function isAjax() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
			&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}

}

